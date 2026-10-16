<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kategori Baru</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('categories.store') }}" id="category-create-form" class="p-6 space-y-6">
                    @csrf

                    @include('categories.form', ['category' => null, 'departments' => $departments])

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                        <a href="{{ route('categories.index') }}"
                           class="rounded-md px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
                        <button type="submit"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
