<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function read(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        abort_unless(
            $notification->notifiable_type === User::class && (int) $notification->notifiable_id === $request->user()->id,
            403,
            'Notifikasi ini bukan milik Anda.',
        );

        $notification->markAsRead();

        $ticketId = $notification->data['ticket_id'] ?? null;

        return redirect($ticketId ? route('tickets.show', $ticketId) : route('dashboard'));
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
