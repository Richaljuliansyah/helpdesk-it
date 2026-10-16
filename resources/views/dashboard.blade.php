<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    Selamat datang, {{ auth()->user()->name }} 👋
                </h2>
                <p class="mt-0.5 text-sm text-gray-500">
                    @if (auth()->user()->isAdmin())
                        Ringkasan seluruh departemen.
                    @elseif (auth()->user()->isAgent())
                        Ringkasan tiket Departemen {{ auth()->user()->department?->name ?? '-' }}.
                    @else
                        Lacak laporan masalah yang pernah Anda buat.
                    @endif
                </p>
            </div>
            <a href="{{ route('tickets.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-indigo-600 to-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-200 hover:from-indigo-500 hover:to-violet-500 transition">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Buat Tiket
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @php $isStaff = auth()->user()->isStaff(); @endphp

        @if ($isStaff)
            {{-- Kartu statistik staf --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">

                @php $scopeLabel = auth()->user()->isAgent() ? 'Departemen ' . (auth()->user()->department?->name ?? '-') : null; @endphp

                <div class="bg-white p-5 sm:rounded-xl shadow-sm ring-1 ring-gray-200/70 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Tiket @if ($scopeLabel)<span class="block text-[11px] text-gray-400">{{ $scopeLabel }}</span>@endif</p>
                            <p class="mt-1 text-3xl font-bold tracking-tight text-gray-900">{{ $stats['total'] }}</p>
                        </div>
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="bg-white p-5 sm:rounded-xl shadow-sm ring-1 ring-gray-200/70 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tiket Baru</p>
                            <p class="mt-1 text-3xl font-bold tracking-tight text-sky-600">{{ $stats['baru'] }}</p>
                        </div>
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.015 1.263l.322.644a2.25 2.25 0 002.015 1.263h3.076a2.25 2.25 0 002.015-1.263l.322-.644a2.25 2.25 0 012.015-1.263h3.859m-19.5.565a24.233 24.233 0 011.723-3.775A24.22 24.22 0 0112 7.5c2.26 0 4.44.4 6.46 1.135a24.23 24.23 0 011.723 3.775M2.25 14.065V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-3.935" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="bg-white p-5 sm:rounded-xl shadow-sm ring-1 ring-gray-200/70 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Sedang Diproses</p>
                            <p class="mt-1 text-3xl font-bold tracking-tight text-amber-600">{{ $stats['diproses'] }}</p>
                        </div>
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="bg-white p-5 sm:rounded-xl shadow-sm ring-1 ring-gray-200/70 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Selesai / Ditutup</p>
                            <p class="mt-1 text-3xl font-bold tracking-tight text-emerald-600">{{ $stats['selesai'] }}</p>
                        </div>
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Ringkasan per departemen (khusus admin global) --}}
            @if (auth()->user()->isAdmin() && isset($deptStats) && $deptStats)
                <div class="bg-white sm:rounded-xl shadow-sm ring-1 ring-gray-200/70 mb-8 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">Tiket per Departemen</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 divide-y sm:divide-y-0 divide-gray-100">
                        @foreach ($deptStats as $dept)
                            <div class="px-6 py-4 flex items-center justify-between gap-3 hover:bg-gray-50/60">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $dept->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $dept->terbuka_count }} terbuka</p>
                                </div>
                                <span class="text-2xl font-bold text-gray-300">{{ $dept->tickets_count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Tiket terbaru staf --}}
            <div class="bg-white sm:rounded-xl shadow-sm ring-1 ring-gray-200/70 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Tiket Terbaru</h3>
                    <a href="{{ route('tickets.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Lihat semua &rarr;</a>
                </div>

                @if ($recentTickets->isEmpty())
                    <div class="p-10 text-center text-gray-500">
                        <p>Belum ada tiket.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50/80">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Kode</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Judul</th>
                                    @if (auth()->user()->isAdmin())
                                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Departemen</th>
                                    @endif
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Pemohon</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Prioritas</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Status</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Diperbarui</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($recentTickets as $ticket)
                                    <tr class="hover:bg-indigo-50/40 transition-colors">
                                        <td class="px-6 py-3 font-mono text-xs text-gray-500">{{ $ticket->ticket_code }}</td>
                                        <td class="px-6 py-3">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-gray-900 hover:text-indigo-600">{{ $ticket->title }}</a>
                                        </td>
                                        @if (auth()->user()->isAdmin())
                                            <td class="px-6 py-3 text-violet-700">{{ $ticket->department?->name ?? '—' }}</td>
                                        @endif
                                        <td class="px-6 py-3 text-gray-600">{{ $ticket->requester->name }}</td>
                                        <td class="px-6 py-3">@include('partials.priority-badge', ['priority' => $ticket->priority])</td>
                                        <td class="px-6 py-3">@include('partials.status-badge', ['status' => $ticket->status])</td>
                                        <td class="px-6 py-3 text-gray-500 whitespace-nowrap">{{ $ticket->updated_at->locale('id')->translatedFormat('d M Y, H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @else
            {{-- Kartu statistik pegawai --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <div class="bg-white p-5 sm:rounded-xl shadow-sm ring-1 ring-gray-200/70 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Semua Tiket Saya</p>
                            <p class="mt-1 text-3xl font-bold tracking-tight text-gray-900">{{ $stats['total'] }}</p>
                        </div>
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="bg-white p-5 sm:rounded-xl shadow-sm ring-1 ring-gray-200/70 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Sedang Diproses</p>
                            <p class="mt-1 text-3xl font-bold tracking-tight text-amber-600">{{ $stats['terbuka'] }}</p>
                        </div>
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="bg-white p-5 sm:rounded-xl shadow-sm ring-1 ring-gray-200/70 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Selesai / Ditutup</p>
                            <p class="mt-1 text-3xl font-bold tracking-tight text-emerald-600">{{ $stats['selesai'] }}</p>
                        </div>
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Tiket terbaru pegawai --}}
            <div class="bg-white sm:rounded-xl shadow-sm ring-1 ring-gray-200/70 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Tiket Saya Terbaru</h3>
                    <a href="{{ route('tickets.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Lihat semua &rarr;</a>
                </div>

                @if ($recentTickets->isEmpty())
                    <div class="p-10 text-center text-gray-500">
                        <p>Belum ada tiket.</p>
                        <a href="{{ route('tickets.create') }}" class="mt-2 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-500">Buat tiket pertama Anda</a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50/80">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Kode</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Judul</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Departemen</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Prioritas</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Status</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Diperbarui</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($recentTickets as $ticket)
                                    <tr class="hover:bg-indigo-50/40 transition-colors">
                                        <td class="px-6 py-3 font-mono text-xs text-gray-500">{{ $ticket->ticket_code }}</td>
                                        <td class="px-6 py-3">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-gray-900 hover:text-indigo-600">{{ $ticket->title }}</a>
                                        </td>
                                        <td class="px-6 py-3 text-violet-700">{{ $ticket->department?->name ?? '—' }}</td>
                                        <td class="px-6 py-3">@include('partials.priority-badge', ['priority' => $ticket->priority])</td>
                                        <td class="px-6 py-3">@include('partials.status-badge', ['status' => $ticket->status])</td>
                                        <td class="px-6 py-3 text-gray-500 whitespace-nowrap">{{ $ticket->updated_at->locale('id')->translatedFormat('d M Y, H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

    </div>
</x-app-layout>
