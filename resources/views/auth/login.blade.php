<x-guest-layout>

    <section class="container-fluid p-0 question1_wrapper"
             id="page_wrapper">

        <div class="row container p-md-0">

            <div class="col-md-6 me-md-5">
                <img src="/frontend/assets/images/f_q_1.png"
                     class="w-100 question_image" alt>
            </div>

            <div
                class="col-md-5 d-flex ms-md-5  gap-4 flex-column">

                <div class="arrow mt-3 mt-md-5 mb-md-5 ">
                    <img src="/frontend/assets/images/logo_green.png" alt="">

                </div>


                <div class="signup_form">
                    <h1>Login To Your Account</h1>
                    <p>Sign in to your personalized reading experience.</p>

                    <form action={{ route('login') }} method="POST" class="d-flex flex-column gap-3 mt-5">
                        @csrf
                        <input type="email" name="email" placeholder="Email address">
                        <input type="password" name="password" placeholder="Password">
                        <input type="submit" value="Login">
                    </form>

                    <p class="text-center">
                        Don't have an account? <a href={{ route('register') }} class="text-decoration-none">Sign up</a>
                    </p>
                </div>
            </div>

        </div>

    </section>


    {{--    <x-authentication-card>--}}
    {{--        <x-slot name="logo">--}}
    {{--            <x-authentication-card-logo />--}}
    {{--        </x-slot>--}}

    {{--        <x-validation-errors class="mb-4" />--}}

    {{--        @session('status')--}}
    {{--            <div class="mb-4 font-medium text-sm text-green-600">--}}
    {{--                {{ $value }}--}}
    {{--            </div>--}}
    {{--        @endsession--}}

    {{--        <form method="POST" action="{{ route('login') }}">--}}
    {{--            @csrf--}}

    {{--            <div>--}}
    {{--                <x-label for="email" value="{{ __('Email') }}" />--}}
    {{--                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />--}}
    {{--            </div>--}}

    {{--            <div class="mt-4">--}}
    {{--                <x-label for="password" value="{{ __('Password') }}" />--}}
    {{--                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />--}}
    {{--            </div>--}}

    {{--            <div class="block mt-4">--}}
    {{--                <label for="remember_me" class="flex items-center">--}}
    {{--                    <x-checkbox id="remember_me" name="remember" />--}}
    {{--                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>--}}
    {{--                </label>--}}
    {{--            </div>--}}

    {{--            <div class="flex items-center justify-end mt-4">--}}
    {{--                @if (Route::has('password.request'))--}}
    {{--                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">--}}
    {{--                        {{ __('Forgot your password?') }}--}}
    {{--                    </a>--}}
    {{--                @endif--}}

    {{--                <x-button class="ms-4">--}}
    {{--                    {{ __('Log in') }}--}}
    {{--                </x-button>--}}
    {{--            </div>--}}
    {{--        </form>--}}
    {{--    </x-authentication-card>--}}
</x-guest-layout>
