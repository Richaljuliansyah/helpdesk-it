@php
    $roleLabels = ['admin' => 'Admin Global', 'agent' => 'Admin Departemen', 'requester' => 'Pegawai'];
    $roleBadges = ['admin' => 'bg-purple-100 text-purple-800 ring-purple-600/20', 'agent' => 'bg-sky-100 text-sky-800 ring-sky-600/20', 'requester' => 'bg-gray-100 text-gray-800 ring-gray-600/20'];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">Manajemen Pengguna</h2>
            <a href="{{ route('users.create') }}"
               class="inline-flex items-center gap-1 rounded-lg bg-gradient-to-r from-indigo-600 to-violet-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm shadow-indigo-200 hover:from-indigo-500 hover:to-violet-500 transition">
                + Pengguna Baru
            </a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-4">

            {{-- Filter --}}
            <form method="GET" action="{{ route('users.index') }}" class="bg-white shadow-sm sm:rounded-lg p-4">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="sm:col-span-2">
                        <label for="q" class="sr-only">Cari</label>
                        <input type="text" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama atau email..."
                               class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>
                    <div class="flex gap-2">
                        <select name="role"
                                class="grow rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Peran</option>
                            @foreach ($roleLabels as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['role'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                                class="shrink-0 rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700">
                            Filter
                        </button>
                    </div>
                </div>
            </form>

            {{-- Tabel --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Nama</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Email</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Peran</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Departemen</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Tiket Dibuat</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Ubah Peran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 font-medium text-gray-900">
                                        {{ $user->name }}
                                        @if ($user->id === auth()->id())
                                            <span class="text-xs text-gray-400">(Anda)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-gray-600">{{ $user->email }}</td>
                                    <td class="px-6 py-3">
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $roleBadges[$user->role] }}">
                                            {{ $roleLabels[$user->role] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3">
                                        @if ($user->department)
                                            <span class="inline-flex items-center rounded-md bg-violet-50 px-2 py-1 text-xs font-medium text-violet-700 ring-1 ring-inset ring-violet-600/20">{{ $user->department->name }}</span>
                                        @elseif ($user->isAdmin())
                                            <span class="text-xs text-gray-400">Semua (global)</span>
                                        @else
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-gray-600">{{ $user->tickets_requested_count }}</td>
                                    <td class="px-6 py-3">
                                        <form method="POST" action="{{ route('users.updateRole', $user) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role"
                                                    class="rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    @if ($user->id === auth()->id()) disabled @endif>
                                                @foreach ($roleLabels as $value => $label)
                                                    <option value="{{ $value }}" {{ $user->role === $value ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="rounded-md bg-gray-800 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-gray-700"
                                                    @if ($user->id === auth()->id()) disabled @endif>
                                                Simpan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="px-1">
                {{ $users->links() }}
            </div>

    </div>
</x-app-layout>
