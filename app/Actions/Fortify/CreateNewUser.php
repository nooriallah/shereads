<?php

namespace App\Actions\Fortify;

use App\Enums\UserRole;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use App\Providers\FortifyServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // The questionnaire always comes before signup (core SHEREADS flow).
        // This also guards direct POSTs that skip the register page redirect.
        if (! FortifyServiceProvider::questionnaireCompleted()) {
            throw ValidationException::withMessages([
                'email' => __('Please answer our short questionnaire first — signup comes right after it.'),
            ]);
        }

        Validator::make($input, [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        // Self-registered users are always subscribers.
        // Never accept a role from public registration input.
        $user = User::create([
            'full_name' => $input['full_name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'role' => UserRole::SUBSCRIBER->value,
        ]);

        $this->attachQuestionnaireResponse($user);

        return $user;
    }

    /**
     * If this visitor answered the questionnaire before signing up,
     * attach that anonymous response to the new account so her answers
     * (and future recommendations) follow her.
     */
    protected function attachQuestionnaireResponse(User $user): void
    {
        $token = session('questionnaire_token');

        if (! $token) {
            return;
        }

        QuestionnaireResponse::where('session_token', $token)
            ->whereNull('user_id')
            ->latest()
            ->first()
            ?->update(['user_id' => $user->id]);
    }
}
