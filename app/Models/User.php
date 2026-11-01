<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use SensitiveParameter;

#[Fillable(['name', 'email', 'password', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function role(): Attribute
    {
        return Attribute::make(
            get: function (): UserRole {
                $code = $this->userRole?->code;
                if ($code === null && ! empty($this->role_id)) {
                    $code = Role::query()->whereKey($this->role_id)->value('code');
                }

                return UserRole::tryFrom((string) $code) ?? UserRole::User;
            },
            set: function (UserRole|string $role): array {
                $this->unsetRelation('userRole');

                return ['role_id' => Role::query()
                    ->where('code', $role instanceof UserRole ? $role->value : $role)
                    ->firstOrFail()
                    ->getKey()];
            },
        );
    }

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
            'status' => UserStatus::class,
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function userRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function authorApplications(): HasMany
    {
        return $this->hasMany(AuthorApplication::class);
    }

    public function latestAuthorApplication(): HasOne
    {
        return $this->hasOne(AuthorApplication::class)->latestOfMany();
    }

    public function postRequests(): HasMany
    {
        return $this->hasMany(PostRequest::class, 'author_id');
    }

    public function handledPostRequests(): HasMany
    {
        return $this->hasMany(PostRequest::class, 'handled_by');
    }

    /** Send the localized signed verification link through the configured mailer. */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }

    /** Send the localized password-reset link without exposing its sensitive token. */
    public function sendPasswordResetNotification(#[SensitiveParameter] $token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
