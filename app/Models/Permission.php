<?php

namespace App\Models;

use Database\Factories\PermissionFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Collection<User> $users
 * @property-read int|null $users_count
 *
 * @method static PermissionFactory<static> factory($count = null, $state = [])
 * @method static static newModelQuery()
 * @method static static newQuery()
 * @method static static query()
 * @method static static whereCreatedAt($value)
 * @method static static whereId($value)
 * @method static static whereName($value)
 * @method static static whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $casts = [
        'id' => 'int',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
