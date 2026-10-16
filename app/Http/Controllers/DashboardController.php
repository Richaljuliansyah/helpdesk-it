<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isStaff()) {
            $base = Ticket::query();

            if ($user->isAgent()) {
                // Admin departemen: statistik hanya departemennya
                $base->where('department_id', $user->department_id);
            }

            $stats = [
                'total' => (clone $base)->count(),
                'baru' => (clone $base)->where('status', 'baru')->count(),
                'diproses' => (clone $base)->where('status', 'diproses')->count(),
                'selesai' => (clone $base)->whereIn('status', ['selesai', 'ditutup'])->count(),
            ];

            $recentTickets = (clone $base)->with(['category', 'requester', 'department'])
                ->latest('updated_at')
                ->limit(8)
                ->get();

            $deptStats = null;
            if ($user->isAdmin()) {
                $deptStats = Department::withCount([
                    'tickets',
                    'tickets as terbuka_count' => fn ($q) => $q->whereNotIn('status', ['selesai', 'ditutup']),
                ])->orderBy('name')->get();
            }

            return view('dashboard', compact('stats', 'recentTickets', 'deptStats'));
        }

        $stats = [
            'total' => $user->ticketsRequested()->count(),
            'terbuka' => $user->ticketsRequested()->open()->count(),
            'selesai' => $user->ticketsRequested()->whereIn('status', ['selesai', 'ditutup'])->count(),
        ];

        $recentTickets = $user->ticketsRequested()
            ->with(['category', 'department'])
            ->latest('updated_at')
            ->limit(8)
            ->get();

        return view('dashboard', compact('stats', 'recentTickets'));
    }
}
