<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Lupa kata sandi?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Masuk') }}
            </x-primary-button>
        </div>

        <!-- Akun demo -->
        <div class="mt-6 rounded-xl bg-indigo-50/70 p-4 ring-1 ring-inset ring-indigo-100">
            <p class="text-xs font-semibold text-indigo-900">Akun demo — kata sandi: <span class="font-mono">password</span></p>
            <ul class="mt-2 space-y-1 text-xs text-indigo-800">
                <li><span class="font-mono">admin@helpdesk.test</span> · Admin Global</li>
                <li><span class="font-mono">budi@helpdesk.test</span> · Admin Dept. IT</li>
                <li><span class="font-mono">hr@helpdesk.test</span> · Admin Dept. HR</li>
                <li><span class="font-mono">andi@helpdesk.test</span> · Pegawai</li>
            </ul>
        </div>
    </form>
</x-guest-layout>
