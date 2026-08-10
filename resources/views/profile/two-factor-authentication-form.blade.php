<div class="card mb-4">
    <div class="card-header d-block">
        <h4 class="card-title mb-1">{{ __('Two Factor Authentication') }}</h4>
        <small class="text-muted">{{ __('Add additional security to your account using two factor authentication.') }}</small>
    </div>

    <div class="card-body">

        <div class="d-flex align-items-center gap-2 mb-3">
            @if ($this->enabled)
                @if ($showingConfirmation)
                    <span class="badge badge-warning">{{ __('Pending confirmation') }}</span>
                    <span class="font-w600">{{ __('Finish enabling two factor authentication.') }}</span>
                @else
                    <span class="badge badge-success">{{ __('Enabled') }}</span>
                    <span class="font-w600">{{ __('You have enabled two factor authentication.') }}</span>
                @endif
            @else
                <span class="badge badge-secondary">{{ __('Disabled') }}</span>
                <span class="font-w600">{{ __('You have not enabled two factor authentication.') }}</span>
            @endif
        </div>

        <p class="text-muted" style="max-width: 640px;">
            {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
        </p>

        @if ($this->enabled)
            @if ($showingQrCode)
                <p class="font-w600" style="max-width: 640px;">
                    @if ($showingConfirmation)
                        {{ __('To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.') }}
                    @else
                        {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application or enter the setup key.') }}
                    @endif
                </p>

                <div class="d-inline-block bg-white border rounded p-3 my-3">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>

                <p class="font-w600">
                    {{ __('Setup Key') }}:
                    <code class="user-select-all">{{ decrypt($this->user->two_factor_secret) }}</code>
                </p>

                @if ($showingConfirmation)
                    <div class="mb-3" style="max-width: 320px;">
                        <x-label for="code" value="{{ __('Code') }}" />
                        <x-input id="code" type="text" name="code" class="form-control-lg" inputmode="numeric"
                            autofocus autocomplete="one-time-code"
                            wire:model="code"
                            wire:keydown.enter="confirmTwoFactorAuthentication" />
                        <x-input-error for="code" class="mt-2" />
                    </div>
                @endif
            @endif

            @if ($showingRecoveryCodes)
                <p class="font-w600" style="max-width: 640px;">
                    {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                </p>

                <div class="bg-light border rounded p-3 font-monospace mb-3" style="max-width: 640px;">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="d-flex flex-wrap gap-2 mt-3">
            @if (! $this->enabled)
                <x-confirms-password wire:then="enableTwoFactorAuthentication">
                    <x-button type="button" wire:loading.attr="disabled">
                        {{ __('Enable') }}
                    </x-button>
                </x-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-confirms-password wire:then="regenerateRecoveryCodes">
                        <x-secondary-button>
                            {{ __('Regenerate Recovery Codes') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @elseif ($showingConfirmation)
                    <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                        <x-button type="button" wire:loading.attr="disabled">
                            {{ __('Confirm') }}
                        </x-button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="showRecoveryCodes">
                        <x-secondary-button>
                            {{ __('Show Recovery Codes') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @endif

                @if ($showingConfirmation)
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <x-secondary-button wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <x-danger-button wire:loading.attr="disabled">
                            {{ __('Disable') }}
                        </x-danger-button>
                    </x-confirms-password>
                @endif
            @endif
        </div>

    </div>
</div>
