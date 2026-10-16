<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Department;
use App\Models\Ticket;
use App\Notifications\TicketNotification;
use App\Traits\IndonesianValidationMessages;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketController extends Controller
{
    use IndonesianValidationMessages;

    public function index(Request $request): View
    {
        $user = $request->user();
        $filters = $request->only(['status', 'priority', 'category_id', 'department_id', 'q']);

        $query = Ticket::with(['category', 'requester', 'department'])
            ->filter($filters)
            ->latest('updated_at');

        if (! $user->isStaff()) {
            $query->where('requester_id', $user->id);
        } elseif ($user->isAgent()) {
            // Admin departemen hanya melihat tiket departemennya
            $query->where('department_id', $user->department_id);
            unset($filters['department_id']);
        }

        $tickets = $query->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('tickets.index', compact('tickets', 'categories', 'departments', 'filters'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('tickets.create', compact('departments', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:5000'],
            'department_id' => ['required', 'exists:departments,id'],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(
                    fn ($q) => $q->where('department_id', $request->input('department_id'))
                ),
            ],
            'priority' => ['required', 'in:'.implode(',', Ticket::PRIORITIES)],
        ];

        $validated = $request->validate($rules, $this->validationMessages(), [
            'category_id.exists' => 'Kategori tidak sesuai dengan departemen yang dipilih.',
        ]);

        // Coba buat tiket dengan retry jika terjadi tabrakan ticket_code (unique)
        // akibat dua request yang membuat tiket pada saat bersamaan.
        $ticket = null;
        foreach (range(1, 5) as $attempt) {
            try {
                $ticket = DB::transaction(fn () => Ticket::create($validated + [
                    'ticket_code' => Ticket::nextTicketCode(),
                    'requester_id' => $request->user()->id,
                    'status' => 'baru',
                ]));
                break;
            } catch (UniqueConstraintViolationException $e) {
                if ($attempt === 5) {
                    throw $e;
                }
            }
        }

        TicketNotification::notifyStaff(
            $ticket,
            'Tiket baru '.$ticket->ticket_code.' masuk ke departemen '.$ticket->department->name.': '.$ticket->title,
            'created',
            $request->user()->id,
        );

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Tiket '.$ticket->ticket_code.' berhasil dibuat.');
    }

    public function show(Request $request, Ticket $ticket): View
    {
        $user = $request->user();
        $this->authorizeView($user, $ticket);

        $comments = $ticket->comments()->with('user')->get();
        if (! $user->isStaff()) {
            $comments = $comments->where('is_internal', false)->values();
        }

        return view('tickets.show', compact('ticket', 'comments'));
    }

    public function edit(Request $request, Ticket $ticket): View
    {
        $this->authorizeEdit($request->user(), $ticket);
        $departments = Department::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('tickets.edit', compact('ticket', 'departments', 'categories'));
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeEdit($user, $ticket);

        $rules = [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:5000'],
            'department_id' => ['required', 'exists:departments,id'],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(
                    fn ($q) => $q->where('department_id', $request->input('department_id'))
                ),
            ],
            'priority' => ['required', 'in:'.implode(',', Ticket::PRIORITIES)],
        ];

        // Estimasi pengerjaan hanya ditentukan oleh admin/staf
        if ($user->isStaff()) {
            $rules['estimated_duration'] = ['nullable', 'in:'.implode(',', array_keys(Ticket::ESTIMATE_LABELS))];
        }

        if ($user->isStaff()) {
            $rules['status'] = ['required', 'in:'.implode(',', Ticket::STATUSES)];
        }

        $validated = $request->validate($rules, $this->validationMessages(), [
            'category_id.exists' => 'Kategori tidak sesuai dengan departemen yang dipilih.',
        ]);

        $oldStatus = $ticket->status;

        if ($user->isStaff()) {
            $validated['started_at'] = $this->startedTimestamp($ticket, $validated['status']);
            $validated['resolved_at'] = $this->resolveTimestamp($ticket, $validated['status']);
        }

        $ticket->update($validated);

        if ($user->isStaff() && $validated['status'] !== $oldStatus && $ticket->requester_id !== $user->id) {
            TicketNotification::notifyUser(
                $ticket->requester,
                $ticket,
                'Status tiket '.$ticket->ticket_code.' diperbarui menjadi "'.Ticket::STATUS_LABELS[$ticket->status].'".',
                'status',
            );
        }

        return redirect()->route('tickets.show', $ticket)->with('success', 'Tiket berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        if (! $request->user()->canHandleTicket($ticket)) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', Ticket::STATUSES)],
        ], $this->validationMessages());

        $oldStatus = $ticket->status;

        $ticket->update([
            'status' => $validated['status'],
            'started_at' => $this->startedTimestamp($ticket, $validated['status']),
            'resolved_at' => $this->resolveTimestamp($ticket, $validated['status']),
        ]);

        if ($validated['status'] !== $oldStatus && $ticket->requester_id !== $request->user()->id) {
            TicketNotification::notifyUser(
                $ticket->requester,
                $ticket,
                'Status tiket '.$ticket->ticket_code.' diperbarui menjadi "'.Ticket::STATUS_LABELS[$validated['status']].'".',
                'status',
            );
        }

        return back()->with('success', 'Status tiket diperbarui menjadi "'.Ticket::STATUS_LABELS[$validated['status']].'".');
    }

    public function updateWorktime(Request $request, Ticket $ticket): RedirectResponse
    {
        if (! $request->user()->canHandleTicket($ticket)) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        $validated = $request->validate([
            'started_at' => ['nullable', 'date'],
            'resolved_at' => ['nullable', 'date', 'after_or_equal:started_at'],
        ], $this->validationMessages(), [
            'resolved_at.after_or_equal' => 'Waktu selesai tidak boleh sebelum waktu mulai dikerjakan.',
        ]);

        $ticket->update([
            'started_at' => $validated['started_at'] ?? null,
            'resolved_at' => $validated['resolved_at'] ?? null,
        ]);

        return back()->with('success', 'Waktu pengerjaan berhasil diperbarui.');
    }

    public function destroy(Request $request, Ticket $ticket): RedirectResponse
    {
        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Tiket berhasil dihapus.');
    }

    private function authorizeView($user, Ticket $ticket): void
    {
        if ($user->isStaff()) {
            if (! $user->canHandleTicket($ticket)) {
                abort(403, 'Tiket ini di luar departemen Anda.');
            }

            return;
        }

        if ($ticket->requester_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }
    }

    private function authorizeEdit($user, Ticket $ticket): void
    {
        $this->authorizeView($user, $ticket);

        if (! $user->isStaff() && $ticket->status !== 'baru') {
            abort(403, 'Tiket sudah diproses dan tidak dapat diubah lagi. Hubungi tim departemen terkait melalui kolom komentar.');
        }
    }

    private function resolveTimestamp(Ticket $ticket, string $newStatus): ?\DateTimeInterface
    {
        if (! in_array($newStatus, ['selesai', 'ditutup'])) {
            return null;
        }

        return $ticket->resolved_at ?? now();
    }

    private function startedTimestamp(Ticket $ticket, string $newStatus): ?\DateTimeInterface
    {
        if ($newStatus === 'diproses') {
            return $ticket->started_at ?? now();
        }

        return $ticket->started_at;
    }
}
