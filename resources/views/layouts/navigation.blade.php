@php
    $roleLabels = ['admin' => 'Admin Global', 'agent' => 'Admin Dept. ' . (auth()->user()->department?->name ?? '-'), 'requester' => 'Pegawai'];
@endphp
<nav x-data="{ open: false }" class="sticky top-0 z-30 bg-white/95 backdrop-blur border-b border-gray-200 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-gray-800 font-semibold text-lg">
                        <svg class="h-7 w-7 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l1.57.393A9.065 9.065 0 0 1 12 21a9.065 9.065 0 0 1-9.37-5.307L5 14.5m14.8.8 1.402 1.402c1.232-1.906 1.651-4.225.876-6.45M5 14.5l-1.402 1.402c-.75-2.123-.37-4.382.876-6.45" />
                        </svg>
                        <span class="hidden sm:inline">Helpdesk IT</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                    <x-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.*')">
                        Tiket
                    </x-nav-link>
                    @if (auth()->user()->isAdmin())
                        <x-nav-link :href="route('departments.index')" :active="request()->routeIs('departments.*')">
                            Departemen
                        </x-nav-link>
                    @endif
                    @if (auth()->user()->isStaff())
                        <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                            Kategori
                        </x-nav-link>
                    @endif
                    @if (auth()->user()->isAdmin())
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                            Pengguna
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:gap-3">
                @include('partials.notifications-bell')

                <a href="{{ route('tickets.create') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-indigo-600 to-violet-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm shadow-indigo-200 hover:from-indigo-500 hover:to-violet-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Buat Tiket
                </a>

                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <span class="ms-2 inline-flex items-center rounded-md bg-gray-100 px-1.5 py-0.5 text-[11px] font-medium text-gray-600">
                                {{ $roleLabels[Auth::user()->role] }}
                            </span>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.*')">
                Tiket
            </x-responsive-nav-link>
            @if (auth()->user()->isAdmin())
                <x-responsive-nav-link :href="route('departments.index')" :active="request()->routeIs('departments.*')">
                    Departemen
                </x-responsive-nav-link>
            @endif
            @if (auth()->user()->isStaff())
                <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                    Kategori
                </x-responsive-nav-link>
            @endif
            @if (auth()->user()->isAdmin())
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    Pengguna
                </x-responsive-nav-link>
            @endif
            <div class="pt-2">
                <a href="{{ route('tickets.create') }}"
                   class="block w-full rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-medium text-white hover:bg-indigo-500">
                    + Buat Tiket
                </a>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ $roleLabels[Auth::user()->role] }} · {{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profil
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        Keluar
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
