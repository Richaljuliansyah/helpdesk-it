<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">Departemen</h2>
            <a href="{{ route('departments.create') }}"
               class="inline-flex items-center gap-1 rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                + Departemen Baru
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl ring-1 ring-gray-200/70">
            @if ($departments->isEmpty())
                <div class="p-10 text-center text-gray-500">
                    <p>Belum ada departemen.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Nama</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Deskripsi</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Kategori</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Tiket</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Staf</th>
                                <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wide text-xs">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($departments as $department)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 font-medium text-gray-900">{{ $department->name }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ $department->description ?? '—' }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ $department->categories_count }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ $department->tickets_count }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ $department->users_count }}</td>
                                    <td class="px-6 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('departments.edit', $department) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Ubah</a>
                                        <form method="POST" action="{{ route('departments.destroy', $department) }}" class="inline ms-3"
                                              onsubmit="return confirm('Hapus departemen &quot;{{ $department->name }}&quot;?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-500">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
