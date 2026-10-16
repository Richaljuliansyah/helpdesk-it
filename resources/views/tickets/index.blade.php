<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ auth()->user()->isStaff() ? 'Semua Tiket' : 'Tiket Saya' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Filter & pencarian --}}
            <form method="GET" action="{{ route('tickets.index') }}" class="bg-white shadow-sm sm:rounded-lg p-4">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
                    <div class="lg:col-span-2">
                        <label for="q" class="sr-only">Cari</label>
                        <input type="text" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari kode, judul, atau isi tiket..."
                               class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>
                    <div>
                        <label for="status" class="sr-only">Status</label>
                        <select id="status" name="status"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Status</option>
                            @foreach (\App\Models\Ticket::STATUS_LABELS as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="priority" class="sr-only">Prioritas</label>
                        <select id="priority" name="priority"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Prioritas</option>
                            @foreach (\App\Models\Ticket::PRIORITY_LABELS as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['priority'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if (auth()->user()->isAdmin())
                        <div>
                            <label for="department_id" class="sr-only">Departemen</label>
                            <select id="department_id" name="department_id"
                                    class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Departemen</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ (string) ($filters['department_id'] ?? '') === (string) $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="flex gap-2">
                        <div class="grow">
                            <label for="category_id" class="sr-only">Kategori</label>
                            <select id="category_id" name="category_id"
                                    class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string) ($filters['category_id'] ?? '') === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                                class="shrink-0 rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700">
                            Filter
                        </button>
                    </div>
                </div>
            </form>

            {{-- Tabel tiket --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if ($tickets->isEmpty())
                    <div class="p-10 text-center text-gray-500">
                        <p>Tidak ada tiket yang cocok.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Kode</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Judul</th>
                                    @if (auth()->user()->isAdmin())
                                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Departemen</th>
                                    @endif
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Kategori</th>
                                    @if (auth()->user()->isStaff())
                                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Pemohon</th>
                                    @endif
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Teknisi</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Prioritas</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Status</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Diperbarui</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($tickets as $ticket)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 font-mono text-xs whitespace-nowrap">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="text-gray-500 hover:text-indigo-600">{{ $ticket->ticket_code }}</a>
                                        </td>
                                        <td class="px-6 py-3 max-w-xs">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-gray-900 hover:text-indigo-600">{{ $ticket->title }}</a>
                                        </td>
                                        @if (auth()->user()->isAdmin())
                                            <td class="px-6 py-3">
                                                <span class="inline-flex items-center rounded-md bg-violet-50 px-2 py-1 text-xs font-medium text-violet-700 ring-1 ring-inset ring-violet-600/20">{{ $ticket->department?->name ?? '—' }}</span>
                                            </td>
                                        @endif
                                        <td class="px-6 py-3 text-gray-600">{{ $ticket->category->name }}</td>
                                        @if (auth()->user()->isStaff())
                                            <td class="px-6 py-3 text-gray-600">{{ $ticket->requester->name }}</td>
                                        @endif
                                        <td class="px-6 py-3 text-gray-600">{{ $ticket->agent?->name ?? '—' }}</td>
                                        <td class="px-6 py-3">@include('partials.priority-badge', ['priority' => $ticket->priority])</td>
                                        @if (auth()->user()->isStaff())
                                            <td class="px-6 py-3">
                                                <div x-data="{ open: false }" class="relative">
                                                    <button type="button" @click="open = !open"
                                                            class="inline-flex cursor-pointer items-center gap-1 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset transition hover:opacity-80 {{ \App\Models\Ticket::STATUS_BADGES[$ticket->status] }}"
                                                            title="Klik untuk mengubah status">
                                                        {{ \App\Models\Ticket::STATUS_LABELS[$ticket->status] }}
                                                        <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                        </svg>
                                                    </button>
                                                    <div x-show="open" @click.outside="open = false" x-transition
                                                         class="absolute right-0 z-20 mt-1 w-32 rounded-lg bg-white py-1 shadow-lg ring-1 ring-gray-200">
                                                        <p class="px-3 pb-1 pt-1.5 text-[11px] font-semibold uppercase tracking-wide text-gray-400">Ubah status</p>
                                                        @foreach (\App\Models\Ticket::STATUS_LABELS as $value => $label)
                                                            <form method="POST" action="{{ route('tickets.status', $ticket) }}">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="{{ $value }}" />
                                                                <button type="submit"
                                                                        class="flex w-full items-center justify-between gap-2 px-3 py-1.5 text-xs {{ $ticket->status === $value ? 'font-semibold text-indigo-600' : 'text-gray-700' }} hover:bg-gray-50">
                                                                    {{ $label }}
                                                                    @if ($ticket->status === $value)
                                                                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                                                        </svg>
                                                                    @endif
                                                                </button>
                                                            </form>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </td>
                                        @else
                                            <td class="px-6 py-3">@include('partials.status-badge', ['status' => $ticket->status])</td>
                                        @endif
                                        <td class="px-6 py-3 text-gray-500 whitespace-nowrap">{{ $ticket->updated_at->locale('id')->translatedFormat('d M Y, H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="px-1">
                {{ $tickets->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
