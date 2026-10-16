<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public string $message,
        public string $type,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_code' => $this->ticket->ticket_code,
            'title' => $this->ticket->title,
            'message' => $this->message,
            'type' => $this->type,
        ];
    }

    /**
     * Beri tahu admin global + admin departemen pemilik tiket.
     */
    public static function notifyStaff(Ticket $ticket, string $message, string $type, ?int $exceptUserId = null): void
    {
        User::query()
            ->where(function ($q) use ($ticket) {
                $q->where(function ($qq) use ($ticket) {
                    $qq->where('role', 'agent')->where('department_id', $ticket->department_id);
                })->orWhere('role', 'admin');
            })
            ->when($exceptUserId, fn ($q, $v) => $q->where('id', '!=', $v))
            ->get()
            ->each(fn (User $u) => $u->notify(new static($ticket, $message, $type)));
    }

    public static function notifyUser(?User $user, Ticket $ticket, string $message, string $type): void
    {
        if ($user) {
            $user->notify(new static($ticket, $message, $type));
        }
    }
}
