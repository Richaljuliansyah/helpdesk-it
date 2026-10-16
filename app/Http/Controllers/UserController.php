<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Traits\IndonesianValidationMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController
{
    use IndonesianValidationMessages;

    public function index(Request $request): View
    {
        $filters = $request->only(['role', 'q']);

        $users = User::query()
            ->when($filters['role'] ?? null, fn ($q, $v) => $q->where('role', $v))
            ->when($filters['q'] ?? null, function ($q, $v) {
                $q->where(function ($qq) use ($v) {
                    $qq->where('name', 'like', "%{$v}%")
                        ->orWhere('email', 'like', "%{$v}%");
                });
            })
            ->with('department')
            ->withCount(['ticketsRequested', 'ticketsAssigned'])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'filters'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:admin,agent,requester'],
            'department_id' => ['nullable', 'required_if:role,agent', 'exists:departments,id'],
        ], $this->validationMessages(), [
            'department_id.required_if' => 'Admin departemen wajib memilih departemennya.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'department_id' => $validated['department_id'] ?? null,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna "'.$validated['name'].'" berhasil ditambahkan.');
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:admin,agent,requester'],
        ], $this->validationMessages());

        if ($user->id === $request->user()->id && $validated['role'] !== 'admin') {
            return back()->with('error', 'Anda tidak dapat menurunkan peran akun Anda sendiri.');
        }

        $user->update($validated);

        return back()->with('success', 'Peran '.$user->name.' diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $ticketCount = Ticket::where(fn ($q) => $q->where('requester_id', $user->id)->orWhere('agent_id', $user->id))->count();

        if ($ticketCount > 0) {
            return back()->with('error', 'Pengguna masih terkait dengan '.$ticketCount.' tiket dan tidak dapat dihapus.');
        }

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
