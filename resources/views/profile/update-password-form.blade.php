<div class="card mb-4">
    <div class="card-header d-block">
        <h4 class="card-title mb-1">{{ __('Update Password') }}</h4>
        <small class="text-muted">{{ __('Ensure your account is using a long, random password to stay secure.') }}</small>
    </div>

    <div class="card-body">
        <form wire:submit.prevent="updatePassword">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <x-label for="current_password" value="{{ __('Current Password') }}" />
                    <x-input id="current_password" type="password" class="form-control-lg"
                        wire:model="state.current_password" autocomplete="current-password" />
                    <x-input-error for="current_password" class="mt-2" />
                </div>

                <div class="col-md-4 mb-3">
                    <x-label for="password" value="{{ __('New Password') }}" />
                    <x-input id="password" type="password" class="form-control-lg"
                        wire:model="state.password" autocomplete="new-password" />
                    <x-input-error for="password" class="mt-2" />
                </div>

                <div class="col-md-4 mb-3">
                    <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                    <x-input id="password_confirmation" type="password" class="form-control-lg"
                        wire:model="state.password_confirmation" autocomplete="new-password" />
                    <x-input-error for="password_confirmation" class="mt-2" />
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-2">
                <x-action-message on="saved">
                    {{ __('Saved.') }}
                </x-action-message>

                <x-button>
                    {{ __('Save') }}
                </x-button>
            </div>
        </form>
    </div>
</div>
