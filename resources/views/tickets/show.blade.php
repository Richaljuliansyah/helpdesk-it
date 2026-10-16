@php
    $user = auth()->user();
    $isStaff = $user->isStaff();
    $canEdit = $isStaff || $ticket->status === 'baru';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span class="font-mono text-base text-gray-500 me-1">{{ $ticket->ticket_code }}</span>{{ $ticket->title }}
            </h2>
            @include('partials.status-badge', ['status' => $ticket->status])
            @include('partials.priority-badge', ['priority' => $ticket->priority])
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Kolom utama --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-start justify-between gap-4">
                            <h3 class="font-semibold text-gray-800">Deskripsi Masalah</h3>
                            @if ($canEdit)
                                <a href="{{ route('tickets.edit', $ticket) }}"
                                   class="shrink-0 inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z" />
                                    </svg>
                                    Ubah
                                </a>
                            @endif
                        </div>
                        <p class="mt-3 text-gray-700 whitespace-pre-line leading-relaxed">{{ $ticket->description }}</p>
                        <p class="mt-4 text-xs text-gray-400">
                            Dibuat oleh {{ $ticket->requester->name }} · {{ $ticket->created_at->locale('id')->translatedFormat('d F Y, H:i') }}
                        </p>
                    </div>

                    {{-- Komentar --}}
                    <div class="bg-white shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-800">Komentar ({{ $comments->count() }})</h3>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @forelse ($comments as $comment)
                                <div class="px-6 py-4 {{ $comment->is_internal ? 'bg-amber-50/60' : '' }}">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">
                                            {{ mb_strtoupper(mb_substr($comment->user->name, 0, 1)) }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</span>
                                        @if ($comment->user->isStaff())
                                            <span class="text-[11px] rounded-md bg-gray-100 px-1.5 py-0.5 font-medium text-gray-600">
                                                {{ $comment->user->isAdmin() ? 'Admin Global' : 'Admin Dept. ' . ($comment->user->department?->name ?? '-') }}
                                            </span>
                                        @endif
                                        @if ($comment->is_internal)
                                            <span class="text-[11px] rounded-md bg-amber-100 px-1.5 py-0.5 font-medium text-amber-800">Internal</span>
                                        @endif
                                        <span class="text-xs text-gray-400 ms-auto">{{ $comment->created_at->locale('id')->translatedFormat('d M Y, H:i') }}</span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $comment->body }}</p>
                                </div>
                            @empty
                                <div class="px-6 py-8 text-center text-sm text-gray-500">Belum ada komentar.</div>
                            @endforelse
                        </div>

                        <form method="POST" action="{{ route('comments.store', $ticket) }}" class="p-6 border-t border-gray-100 space-y-3">
                            @csrf
                            <div>
                                <label for="body" class="block font-medium text-sm text-gray-700">Tambahkan Komentar</label>
                                <textarea id="body" name="body" rows="3" required maxlength="3000"
                                          placeholder="Tulis balasan atau informasi tambahan..."
                                          class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('body') border-red-500 @enderror">{{ old('body') }}</textarea>
                                @error('body') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            @if ($isStaff)
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" name="is_internal" value="1" {{ old('is_internal') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-amber-600 focus:ring-amber-500" />
                                    Tandai sebagai catatan internal (tidak terlihat oleh pemohon)
                                </label>
                            @endif
                            <div class="flex justify-end">
                                <button type="submit"
                                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                                    Kirim Komentar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                        <h3 class="font-semibold text-gray-800">Informasi Tiket</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Kode</dt>
                                <dd class="font-mono text-gray-900">{{ $ticket->ticket_code }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Departemen</dt>
                                <dd class="text-gray-900">{{ $ticket->department?->name ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Kategori</dt>
                                <dd class="text-gray-900">{{ $ticket->category->name }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Prioritas</dt>
                                <dd>@include('partials.priority-badge', ['priority' => $ticket->priority])</dd>
                            </div>
                            @if ($ticket->estimated_duration)
                                <div class="flex justify-between gap-4">
                                    <dt class="text-gray-500">Estimasi pengerjaan</dt>
                                    <dd class="text-right text-gray-900">
                                        {{ \App\Models\Ticket::ESTIMATE_LABELS[$ticket->estimated_duration] }}
                                        @if ($ticket->isOverEstimate())
                                            <span class="block text-[11px] font-medium text-red-600">melebihi estimasi</span>
                                        @endif
                                    </dd>
                                </div>
                            @endif
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Status</dt>
                                <dd>@include('partials.status-badge', ['status' => $ticket->status])</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Pemohon</dt>
                                <dd class="text-right text-gray-900">{{ $ticket->requester->name }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Teknisi</dt>
                                <dd class="text-right {{ $ticket->agent ? 'text-gray-900' : 'text-amber-600 font-medium' }}">
                                    {{ $ticket->agent?->name ?? 'Belum ditugaskan' }}
                                </dd>
                            </div>
                            @if ($ticket->started_at)
                                <div class="flex justify-between gap-4">
                                    <dt class="text-gray-500">Mulai dikerjakan</dt>
                                    <dd class="text-right text-gray-900">{{ $ticket->started_at->locale('id')->translatedFormat('d M Y, H:i') }}</dd>
                                </div>
                            @endif
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Durasi pengerjaan</dt>
                                <dd class="text-right font-medium {{ $ticket->isWorkInProgress() ? 'text-amber-600' : 'text-gray-900' }}">
                                    {{ $ticket->workDuration() }}
                                    @if ($ticket->isWorkInProgress())
                                        <span class="block text-[11px] font-normal text-amber-500">(sedang berjalan)</span>
                                    @endif
                                </dd>
                            </div>
                            @if ($ticket->resolved_at)
                                <div class="flex justify-between gap-4">
                                    <dt class="text-gray-500">Selesai pada</dt>
                                    <dd class="text-right text-gray-900">{{ $ticket->resolved_at->locale('id')->translatedFormat('d M Y, H:i') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    @if ($isStaff)
                        <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                            <h3 class="font-semibold text-gray-800">Aksi Teknisi</h3>

                            <div>
                                <p class="text-sm text-gray-500 mb-2">Ubah status</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach (\App\Models\Ticket::STATUS_LABELS as $value => $label)
                                        @if ($value === $ticket->status)
                                            <span class="inline-flex items-center gap-1 rounded-md px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ \App\Models\Ticket::STATUS_BADGES[$value] }}">
                                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $label }}
                                            </span>
                                        @else
                                            <form method="POST" action="{{ route('tickets.status', $ticket) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $value }}" />
                                                <button type="submit"
                                                        class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                    {{ $label }}
                                                </button>
                                            </form>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-sm text-gray-500 mb-2">Waktu pengerjaan</p>
                                <form id="worktime-form" method="POST" action="{{ route('tickets.worktime', $ticket) }}" class="space-y-3">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label for="started_at" class="block text-xs font-medium text-gray-600">Mulai dikerjakan</label>
                                        <input id="started_at" type="datetime-local" name="started_at"
                                               value="{{ $ticket->started_at?->format('Y-m-d\TH:i') }}"
                                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('started_at') border-red-500 @enderror" />
                                        @error('started_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="resolved_at" class="block text-xs font-medium text-gray-600">Selesai pada</label>
                                        <input id="resolved_at" type="datetime-local" name="resolved_at"
                                               value="{{ $ticket->resolved_at?->format('Y-m-d\TH:i') }}"
                                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('resolved_at') border-red-500 @enderror" />
                                        @error('resolved_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <p class="text-[11px] text-gray-400">Kosongkan untuk menghapus. Durasi pengerjaan dihitung otomatis dari kedua waktu ini.</p>
                                    <button type="submit"
                                            class="w-full rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                                        Simpan Waktu
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if ($user->isAdmin())
                        <div class="bg-white shadow-sm sm:rounded-lg p-6">
                            <h3 class="font-semibold text-gray-800 mb-3">Zona Berbahaya</h3>
                            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}"
                                  onsubmit="return confirm('Hapus tiket {{ $ticket->ticket_code }}? Tindakan ini tidak dapat dibatalkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full rounded-md border border-red-300 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                                    Hapus Tiket
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
