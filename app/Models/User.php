<?php

namespace App\Models;

use Database\Factories\UserFactory;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @property int $id
 * @property string $alias
 * @property string $name
 * @property string $email
 * @property \Carbon\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read DatabaseNotificationCollection<DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static UserFactory<static> factory($count = null, $state = [])
 * @method static static newModelQuery()
 * @method static static newQuery()
 * @method static static query()
 * @method static static whereAlias($value)
 * @method static static whereCreatedAt($value)
 * @method static static whereEmail($value)
 * @method static static whereEmailVerifiedAt($value)
 * @method static static whereId($value)
 * @method static static whereName($value)
 * @method static static wherePassword($value)
 * @method static static whereRememberToken($value)
 * @method static static whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $resourceType = 'authors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'alias',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
}
