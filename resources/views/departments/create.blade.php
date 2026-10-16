<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">Departemen Baru</h2>
    </x-slot>

    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white shadow-sm sm:rounded-xl ring-1 ring-gray-200/70">
            <form method="POST" action="{{ route('departments.store') }}" class="p-6 space-y-6">
                @csrf

                @include('departments.form', ['department' => null])

                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                    <a href="{{ route('departments.index') }}"
                       class="rounded-md px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
