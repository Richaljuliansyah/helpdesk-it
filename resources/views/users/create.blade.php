@php
    $roleLabels = ['admin' => 'Admin Global', 'agent' => 'Admin Departemen', 'requester' => 'Pegawai'];
    $roleDescriptions = [
        'admin' => 'Akses penuh ke semua departemen: tiket, kategori, departemen, dan pengguna.',
        'agent' => 'Admin departemen: hanya melihat dan menangani tiket departemennya sendiri.',
        'requester' => 'Pegawai biasa: membuat tiket dan melacak statusnya.',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">Tambah Pengguna Baru</h2>
    </x-slot>

    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white shadow-sm sm:rounded-xl ring-1 ring-gray-200/70">
            <form method="POST" action="{{ route('users.store') }}" class="p-6 space-y-6">
                @csrf

                <div>
                    <label for="name" class="block font-medium text-sm text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text" required maxlength="255" value="{{ old('name') }}"
                           placeholder="Contoh: Rina Kartika"
                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror" />
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block font-medium text-sm text-gray-700">Email <span class="text-red-500">*</span></label>
                    <input id="email" name="email" type="email" required maxlength="255" value="{{ old('email') }}"
                           placeholder="contoh@perusahaan.co.id"
                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-500 @enderror" />
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="password" class="block font-medium text-sm text-gray-700">Kata Sandi <span class="text-red-500">*</span></label>
                        <input id="password" name="password" type="password" required autocomplete="new-password"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('password') border-red-500 @enderror" />
                        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Ulangi Kata Sandi <span class="text-red-500">*</span></label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>
                </div>

                <div>
                    <label for="role" class="block font-medium text-sm text-gray-700">Peran <span class="text-red-500">*</span></label>
                    <select id="role" name="role" required
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('role') border-red-500 @enderror">
                        @foreach ($roleLabels as $value => $label)
                            <option value="{{ $value }}" {{ old('role') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-2 text-xs text-gray-500">{{ $roleDescriptions[old('role', 'requester')] }}</p>
                </div>

                <div>
                    <label for="department_id" class="block font-medium text-sm text-gray-700">
                        Departemen <span class="text-red-500">* wajib untuk Admin Departemen</span>
                    </label>
                    <select id="department_id" name="department_id"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('department_id') border-red-500 @enderror">
                        <option value="">— Tanpa departemen —</option>
                        @foreach (\App\Models\Department::orderBy('name')->get() as $department)
                            <option value="{{ $department->id }}" {{ (string) old('department_id') === (string) $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                    <a href="{{ route('users.index') }}"
                       class="rounded-md px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
