<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">Kategori Tiket</h2>
                @if (auth()->user()->isAgent())
                    <p class="mt-0.5 text-sm text-gray-500">Departemen {{ auth()->user()->department?->name ?? '-' }}</p>
                @endif
            </div>
            <a href="{{ route('categories.create') }}"
               class="inline-flex items-center gap-1 rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                + Kategori Baru
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl ring-1 ring-gray-200/70">
            @if ($categories->isEmpty())
                <div class="p-10 text-center text-gray-500">
                    <p>Belum ada kategori.</p>
                    <a href="{{ route('categories.create') }}" class="mt-2 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-500">Buat kategori pertama</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Nama</th>
                                @if (auth()->user()->isAdmin())
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Departemen</th>
                                @endif
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Deskripsi</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Jumlah Tiket</th>
                                <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wide text-xs">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($categories as $category)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 font-medium text-gray-900">{{ $category->name }}</td>
                                    @if (auth()->user()->isAdmin())
                                        <td class="px-6 py-3">
                                            <span class="inline-flex items-center rounded-md bg-violet-50 px-2 py-1 text-xs font-medium text-violet-700 ring-1 ring-inset ring-violet-600/20">{{ $category->department?->name ?? '—' }}</span>
                                        </td>
                                    @endif
                                    <td class="px-6 py-3 text-gray-600">{{ $category->description ?? '—' }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ $category->tickets_count }}</td>
                                    <td class="px-6 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('categories.edit', $category) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Ubah</a>
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline ms-3"
                                              onsubmit="return confirm('Hapus kategori &quot;{{ $category->name }}&quot;?');">
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
