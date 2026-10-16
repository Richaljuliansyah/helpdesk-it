<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_code',
        'title',
        'description',
        'department_id',
        'category_id',
        'priority',
        'estimated_duration',
        'status',
        'requester_id',
        'agent_id',
        'started_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public const PRIORITIES = ['rendah', 'sedang', 'tinggi', 'urgent'];

    public const STATUSES = ['baru', 'diproses', 'selesai', 'ditutup'];

    public const STATUS_LABELS = [
        'baru' => 'Baru',
        'diproses' => 'Diproses',
        'selesai' => 'Selesai',
        'ditutup' => 'Ditutup',
    ];

    public const STATUS_BADGES = [
        'baru' => 'bg-sky-100 text-sky-800 ring-sky-600/20',
        'diproses' => 'bg-amber-100 text-amber-800 ring-amber-600/20',
        'selesai' => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
        'ditutup' => 'bg-gray-100 text-gray-800 ring-gray-600/20',
    ];

    public const PRIORITY_LABELS = [
        'rendah' => 'Rendah',
        'sedang' => 'Sedang',
        'tinggi' => 'Tinggi',
        'urgent' => 'Urgent',
    ];

    public const PRIORITY_BADGES = [
        'rendah' => 'bg-gray-100 text-gray-800 ring-gray-600/20',
        'sedang' => 'bg-cyan-100 text-cyan-800 ring-cyan-600/20',
        'tinggi' => 'bg-orange-100 text-orange-800 ring-orange-600/20',
        'urgent' => 'bg-red-100 text-red-800 ring-red-600/20',
    ];

    public const ESTIMATE_LABELS = [
        '<1h' => 'Kurang dari 1 jam',
        '1-4h' => '1 - 4 jam',
        '1-3d' => '1 - 3 hari',
        '>3d' => 'Lebih dari 3 hari',
    ];

    public const ESTIMATE_MAX_MINUTES = [
        '<1h' => 60,
        '1-4h' => 240,
        '1-3d' => 4320,
        '>3d' => null,
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at');
    }

    public function scopeOpen($query)
    {
        return $query->whereNotIn('status', ['selesai', 'ditutup']);
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['priority'] ?? null, fn ($q, $v) => $q->where('priority', $v))
            ->when($filters['category_id'] ?? null, fn ($q, $v) => $q->where('category_id', $v))
            ->when($filters['department_id'] ?? null, fn ($q, $v) => $q->where('department_id', $v))
            ->when($filters['q'] ?? null, function ($q, $v) {
                $q->where(function ($qq) use ($v) {
                    $qq->where('title', 'like', "%{$v}%")
                        ->orWhere('ticket_code', 'like', "%{$v}%")
                        ->orWhere('description', 'like', "%{$v}%");
                });
            });
    }

    public static function nextTicketCode(): string
    {
        $year = now()->format('y');
        $prefix = 'TKT-'.$year.'-';

        // Ambil nomor urut terbesar secara NUMERIK (bukan string sort),
        // agar tetap benar setelah melewati 9999 -> 10000, 10001, dst.
        $lastNumber = (int) static::where('ticket_code', 'like', $prefix.'%')
            ->get(['ticket_code'])
            ->map(fn ($ticket) => (int) substr($ticket->ticket_code, strlen($prefix)))
            ->max();

        $nextNumber = ($lastNumber ?: 0) + 1;

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Durasi pengerjaan: dari mulai dikerjakan (atau dibuat) sampai selesai,
     * atau sampai sekarang untuk tiket yang masih terbuka.
     */
    public function workDuration(): string
    {
        $start = $this->started_at ?? $this->created_at;
        $end = $this->resolved_at ?? now();

        $minutes = (int) floor($start->diffInMinutes($end));

        $days = intdiv($minutes, 1440);
        $hours = intdiv($minutes % 1440, 60);
        $mins = $minutes % 60;

        $parts = [];
        if ($days > 0) {
            $parts[] = $days.' hari';
        }
        if ($hours > 0) {
            $parts[] = $hours.' jam';
        }
        if ($mins > 0 || $parts === []) {
            $parts[] = $mins.' menit';
        }

        return implode(' ', $parts);
    }

    public function isWorkInProgress(): bool
    {
        return $this->status === 'diproses' && $this->resolved_at === null;
    }

    /**
     * True jika tiket masih diproses dan durasi berjalan sudah melewati estimasi.
     */
    public function isOverEstimate(): bool
    {
        if (! $this->estimated_duration || $this->status !== 'diproses') {
            return false;
        }

        $max = self::ESTIMATE_MAX_MINUTES[$this->estimated_duration] ?? null;

        if ($max === null) {
            return false;
        }

        $start = $this->started_at ?? $this->created_at;

        return $start->diffInMinutes(now()) > $max;
    }
}
