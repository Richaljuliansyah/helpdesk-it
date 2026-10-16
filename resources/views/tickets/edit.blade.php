<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm shadow-indigo-200">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
            </span>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ubah Tiket {{ $ticket->ticket_code }}</h2>
                <div class="mt-0.5">@include('partials.status-badge', ['status' => $ticket->status])</div>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-100 sm:rounded-2xl">
                <div class="bg-gradient-to-r from-indigo-50 via-violet-50 to-white px-6 py-4 border-b border-gray-100">
                    <p class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                        <span>Perubahan akan langsung tersimpan. Kolom bertanda <span class="text-red-500">*</span> wajib diisi.</span>
                    </p>
                </div>

                <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="p-6 sm:p-8">
                    @csrf
                    @method('PUT')

                    @include('tickets.form', [
                        'ticket' => $ticket,
                        'departments' => $departments,
                        'categories' => $categories,
                        'showStaffFields' => auth()->user()->isStaff(),
                        'showEstimateField' => auth()->user()->isStaff(),
                    ])

                    <div class="flex items-center justify-between gap-3 border-t border-gray-100 pt-6 mt-8">
                        @if (auth()->user()->isAdmin())
                            <button type="button" x-data
                                    @click="if (confirm('Hapus tiket {{ $ticket->ticket_code }}? Tindakan ini tidak dapat dibatalkan.')) { $root.querySelector('#delete-ticket-form').submit(); }"
                                    class="rounded-lg px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                Hapus Tiket
                            </button>
                            <form id="delete-ticket-form" method="POST" action="{{ route('tickets.destroy', $ticket) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        @else
                            <span></span>
                        @endif
                        <div class="flex items-center gap-3">
                            <a href="{{ route('tickets.show', $ticket) }}"
                               class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Batal</a>
                            <button type="submit"
                                    class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm shadow-indigo-200 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-colors">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
