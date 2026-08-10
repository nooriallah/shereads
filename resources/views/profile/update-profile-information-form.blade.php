<div class="card mb-4">
    <div class="card-header d-block">
        <h4 class="card-title mb-1">{{ __('Profile Information') }}</h4>
        <small class="text-muted">{{ __('Update your account\'s profile information and email address.') }}</small>
    </div>

    <div class="card-body">
        <form wire:submit.prevent="updateProfileInformation">

            <div class="row">
                {{-- Profile Photo --}}
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="col-12 mb-4" x-data="{photoName: null, photoPreview: null}">
                        {{-- Photo File Input (hidden) --}}
                        <input type="file" id="photo" class="d-none"
                            wire:model.live="photo"
                            x-ref="photo"
                            x-on:change="
                                photoName = $refs.photo.files[0].name;
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    photoPreview = e.target.result;
                                };
                                reader.readAsDataURL($refs.photo.files[0]);
                            " />

                        <div class="d-flex align-items-center gap-4">
                            {{-- Current Profile Photo --}}
                            <div x-show="! photoPreview">
                                <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->full_name }}"
                                    class="rounded-circle border"
                                    style="width: 90px; height: 90px; object-fit: cover;">
                            </div>

                            {{-- New Profile Photo Preview --}}
                            <div x-show="photoPreview" style="display: none;">
                                <span class="d-block rounded-circle border"
                                    style="width: 90px; height: 90px; background-size: cover; background-position: center;"
                                    x-bind:style="'background-image: url(\'' + photoPreview + '\'); width: 90px; height: 90px; background-size: cover; background-position: center;'">
                                </span>
                            </div>

                            <div>
                                <span class="form-label font-w500 d-block">{{ __('Photo') }}</span>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        x-on:click.prevent="$refs.photo.click()">
                                        {{ __('Select A New Photo') }}
                                    </button>

                                    @if ($this->user->profile_photo)
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            wire:click="deleteProfilePhoto">
                                            {{ __('Remove Photo') }}
                                        </button>
                                    @endif
                                </div>
                                <x-input-error for="photo" class="mt-2" />
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Full Name --}}
                <div class="col-md-6 mb-3">
                    <x-label for="full_name" value="{{ __('Full Name') }}" />
                    <x-input id="full_name" type="text" class="form-control-lg" wire:model="state.full_name"
                        required autocomplete="name" />
                    <x-input-error for="full_name" class="mt-2" />
                </div>

                {{-- Email --}}
                <div class="col-md-6 mb-3">
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input id="email" type="email" class="form-control-lg" wire:model="state.email"
                        required autocomplete="username" />
                    <x-input-error for="email" class="mt-2" />

                    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                        <p class="small mt-2 mb-0">
                            {{ __('Your email address is unverified.') }}

                            <button type="button" class="btn btn-link btn-sm p-0 align-baseline"
                                style="color:#05653D;" wire:click.prevent="sendEmailVerification">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if ($this->verificationLinkSent)
                            <p class="small text-success font-w600 mt-1 mb-0">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-2">
                <x-action-message on="saved">
                    {{ __('Saved.') }}
                </x-action-message>

                <x-button wire:loading.attr="disabled" wire:target="photo">
                    {{ __('Save') }}
                </x-button>
            </div>
        </form>
    </div>
</div>
