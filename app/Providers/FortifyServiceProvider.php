<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\QuestionnaireResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Core SHEREADS flow: the questionnaire always comes before signup.
        // Anyone opening /register without a completed questionnaire in
        // their session is sent to the start of the journey instead.
        Fortify::registerView(function () {
            if (! static::questionnaireCompleted()) {
                return redirect()
                    ->route('startnow')
                    ->with('message', 'First answer a few quick questions so we can prepare your personalized book list — signup comes right after.');
            }

            return view('auth.register');
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }

    /**
     * Does the current session hold a completed questionnaire response?
     */
    public static function questionnaireCompleted(): bool
    {
        $token = session('questionnaire_token');

        return $token && QuestionnaireResponse::where('session_token', $token)
            ->whereNotNull('completed_at')
            ->exists();
    }
}
