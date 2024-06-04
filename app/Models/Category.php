<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Collection<Article> $articles
 * @property-read int|null $articles_count
 *
 * @method static CategoryFactory<static> factory(...$parameters)
 * @method static static newModelQuery()
 * @method static static newQuery()
 * @method static static query()
 * @method static static whereCreatedAt($value)
 * @method static static whereId($value)
 * @method static static whereName($value)
 * @method static static whereSlug($value)
 * @method static static whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
