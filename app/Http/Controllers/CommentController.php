<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use App\Notifications\TicketNotification;
use App\Traits\IndonesianValidationMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use IndonesianValidationMessages;

    public function store(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = $request->user();

        if ($user->isStaff()) {
            if (! $user->canHandleTicket($ticket)) {
                abort(403, 'Tiket ini di luar departemen Anda.');
            }
        } elseif ($ticket->requester_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:3000'],
            'is_internal' => ['nullable', 'boolean'],
        ], $this->validationMessages());

        Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'body' => $validated['body'],
            'is_internal' => $user->isStaff() && (bool) ($validated['is_internal'] ?? false),
        ]);

        if ($user->isStaff()) {
            // Komentar publik dari staf → beri tahu pemohon; catatan internal tidak
            if (! ($validated['is_internal'] ?? false) && $ticket->requester_id !== $user->id) {
                TicketNotification::notifyUser(
                    $ticket->requester,
                    $ticket,
                    $user->name.' membalas tiket '.$ticket->ticket_code.'.',
                    'comment',
                );
            }
        } else {
            // Komentar dari pemohon → beri tahu admin departemen terkait
            TicketNotification::notifyStaff(
                $ticket,
                $user->name.' berkomentar pada tiket '.$ticket->ticket_code.': '.$ticket->title,
                'comment',
                $user->id,
            );
        }

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
