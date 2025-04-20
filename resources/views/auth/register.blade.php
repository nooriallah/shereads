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
                    <img src="assets/images/logo_green.png" alt="">

                </div>


                <div class="signup_form">
                    <h1>Create Your Account</h1>
                    <p>Sign up to unlock your personalized reading experience.</p>

                    <form action={{ route("register") }} method="POST" class="d-flex flex-column gap-3 mt-5">
                        @csrf
                        <input type="text" name="full_name" placeholder="Full name" />
                        <input type="email" name="email" placeholder="Email address" />
                        <input type="password" name="password" placeholder="Password" />
                        <input type="password" name="password_confirmation" placeholder="Confirm Password" />
                        <input type="submit" value="Sign Up" />

                    </form>
                    <p class="text-center">
                        Already have an account? <a href={{ route('login') }} class="text-decoration-none">Sign In</a>
                    </p>


                </div>


            </div>

        </div>

    </section>


{{--        <x-authentication-card>--}}
{{--            <x-slot name="logo">--}}
{{--                <x-authentication-card-logo />--}}
{{--            </x-slot>--}}

{{--            <x-validation-errors class="mb-4" />--}}

{{--            <form method="POST" action="{{ route('register') }}">--}}
{{--                @csrf--}}

{{--                <div>--}}
{{--                    <x-label for="name" value="{{ __('Name') }}" />--}}
{{--                    <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />--}}
{{--                </div>--}}

{{--                <div class="mt-4">--}}
{{--                    <x-label for="email" value="{{ __('Email') }}" />--}}
{{--                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />--}}
{{--                </div>--}}

{{--                <div class="mt-4">--}}
{{--                    <x-label for="password" value="{{ __('Password') }}" />--}}
{{--                    <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />--}}
{{--                </div>--}}

{{--                <div class="mt-4">--}}
{{--                    <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />--}}
{{--                    <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />--}}
{{--                </div>--}}

{{--                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())--}}
{{--                    <div class="mt-4">--}}
{{--                        <x-label for="terms">--}}
{{--                            <div class="flex items-center">--}}
{{--                                <x-checkbox name="terms" id="terms" required />--}}

{{--                                <div class="ms-2">--}}
{{--                                    {!! __('I agree to the :terms_of_service and :privacy_policy', [--}}
{{--                                            'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',--}}
{{--                                            'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',--}}
{{--                                    ]) !!}--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </x-label>--}}
{{--                    </div>--}}
{{--                @endif--}}

{{--                <div class="flex items-center justify-end mt-4">--}}
{{--                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">--}}
{{--                        {{ __('Already registered?') }}--}}
{{--                    </a>--}}

{{--                    <x-button class="ms-4">--}}
{{--                        {{ __('Register') }}--}}
{{--                    </x-button>--}}
{{--                </div>--}}
{{--            </form>--}}
{{--        </x-authentication-card>--}}
</x-guest-layout>
