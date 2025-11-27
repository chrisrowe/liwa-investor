<x-guest-layout>
    @include('website-nav')
    <x-jet-authentication-card>

        <x-slot name="logo">
            <h2>Reset Password</h2>
        </x-slot>

        <div>
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">Back to login</a>
        </div>

        <x-jet-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="block">
                <x-jet-label for="email" value="{{ __('Email') }}" />
                <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus />
            </div>

            <div class="mt-4">
                <x-jet-label for="password" value="{{ __('Password') }}" />
                <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-jet-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-jet-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="text-right mt-4">

                <x-jet-button>
                    {{ __('Reset Password') }}
                </x-jet-button>

                @if ($errors->any())
                    <div class="mt-3">
                        <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">Token expired? Request another link.</a>
                    </div>
                @endif
            </div>
        </form>
    </x-jet-authentication-card>
    @include('website-footer')

</x-guest-layout>
