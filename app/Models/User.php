<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'role',
        'profile_photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Questionnaire responses attached to this user
     * (linked at signup via the session token).
     */
    public function questionnaireResponses(): HasMany
    {
        return $this->hasMany(QuestionnaireResponse::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Profile photo
    |--------------------------------------------------------------------------
    | These override Jetstream's HasProfilePhoto trait, which expects a
    | `profile_photo_path` column. This application uses `profile_photo`
    | and stores photos in the `profiles` directory on the public disk
    | (the same convention as the admin user management page).
    */

    public function updateProfilePhoto(UploadedFile $photo, $storagePath = 'profiles')
    {
        tap($this->profile_photo, function ($previous) use ($photo, $storagePath) {
            $this->forceFill([
                'profile_photo' => $photo->storePublicly($storagePath, ['disk' => 'public']),
            ])->save();

            if ($previous) {
                Storage::disk('public')->delete($previous);
            }
        });
    }

    public function deleteProfilePhoto()
    {
        if (! $this->profile_photo) {
            return;
        }

        Storage::disk('public')->delete($this->profile_photo);

        $this->forceFill(['profile_photo' => null])->save();
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo && Storage::disk('public')->exists($this->profile_photo)
            ? Storage::disk('public')->url($this->profile_photo)
            : asset('backend/images/usericon.png');
    }
}
