<x-app-layout>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xxl-9 col-12">

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h2 class="fs-24 font-w700 mb-1">{{ __('My Profile') }}</h2>
                        <p class="text-muted mb-0">{{ __('Manage your account information and security settings.') }}</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        {{ __('Back to Dashboard') }}
                    </a>
                </div>

                @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                    @livewire('profile.update-profile-information-form')
                @endif

                {{-- Password change: always available to every user (readers included) --}}
                @livewire('profile.update-password-form')

                @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                    @livewire('profile.two-factor-authentication-form')
                @endif

                @livewire('profile.logout-other-browser-sessions-form')

                @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                    @livewire('profile.delete-user-form')
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
