@php
    $user = auth()->user();
    $unreadCount = $user->unreadNotifications->count();
    $typeDots = [
        'created' => 'bg-sky-500',
        'status' => 'bg-amber-500',
        'comment' => 'bg-indigo-500',
    ];
@endphp
<x-dropdown align="right" width="96 max-w-[calc(100vw-2rem)]">
    <x-slot name="trigger">
        <button type="button"
                class="relative inline-flex items-center justify-center rounded-full p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition ease-in-out duration-150"
                aria-label="Notifikasi">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
            @if ($unreadCount > 0)
                <span class="absolute -top-0.5 -right-0.5 inline-flex h-4.5 min-w-4.5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-white">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </button>
    </x-slot>

    <x-slot name="content">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-800">Notifikasi</p>
            @if ($unreadCount > 0)
                <span class="rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-semibold text-red-600">
                    {{ $unreadCount }} baru
                </span>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse ($user->unreadNotifications->take(8) as $notification)
                <a href="{{ route('notifications.read', $notification->id) }}"
                   class="flex items-start gap-3 px-4 py-3 hover:bg-indigo-50/60 border-b border-gray-100 last:border-b-0 transition-colors duration-100">
                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $typeDots[$notification->data['type'] ?? ''] ?? 'bg-gray-400' }}"></span>
                    <span class="min-w-0">
                        <span class="block text-sm font-medium text-gray-700 leading-snug line-clamp-2">
                            {{ $notification->data['message'] ?? 'Notifikasi' }}
                        </span>
                        <span class="mt-0.5 block text-xs text-gray-400">
                            {{ \Illuminate\Support\Carbon::parse($notification->created_at)->locale('id')->diffForHumans() }}
                        </span>
                    </span>
                </a>
            @empty
                <div class="flex flex-col items-center px-6 py-8 text-center">
                    <svg class="h-8 w-8 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Belum ada notifikasi</p>
                    <p class="text-xs text-gray-400">Notifikasi baru akan muncul di sini.</p>
                </div>
            @endforelse
        </div>

        @if ($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.markAll') }}" class="border-t border-gray-100">
                @csrf
                <button type="submit"
                        class="w-full px-4 py-2.5 text-center text-xs font-semibold text-indigo-600 hover:bg-indigo-50/60 transition-colors duration-100">
                    Tandai semua dibaca
                </button>
            </form>
        @endif
    </x-slot>
</x-dropdown>
