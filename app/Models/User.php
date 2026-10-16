<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'agent']);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Admin departemen hanya boleh menangani tiket departemennya;
     * admin global boleh semua.
     */
    public function canHandleTicket(Ticket $ticket): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->isAgent()
            && $this->department_id !== null
            && $this->department_id === $ticket->department_id;
    }

    public function ticketsRequested()
    {
        return $this->hasMany(Ticket::class, 'requester_id');
    }

    public function ticketsAssigned()
    {
        return $this->hasMany(Ticket::class, 'agent_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
