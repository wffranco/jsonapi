<?php

namespace App\Models;

use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $slug
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Category $category
 * @property-read User $user
 *
 * @method static ArticleFactory<static> factory($count = null, $state = [])
 * @method static static newModelQuery()
 * @method static static newQuery()
 * @method static static query()
 * @method static static whereCategoryId($value)
 * @method static static whereContent($value)
 * @method static static whereCreatedAt($value)
 * @method static static whereId($value)
 * @method static static whereSlug($value)
 * @method static static whereTitle($value)
 * @method static static whereUpdatedAt($value)
 * @method static static whereUserId($value)
 *
 * Scopes:
 * @method static Builder|static day($day)
 * @method static Builder|static month($month)
 * @method static Builder|static year($year)
 *
 * @mixin \Eloquent
 */
class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
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

    /** @param static $query */
    public function scopeDay(Builder $query, $day)
    {
        return $query->whereDay('created_at', $day);
    }

    /** @param static $query */
    public function scopeMonth(Builder $query, $month)
    {
        return $query->whereMonth('created_at', $month);
    }

    /** @param static $query */
    public function scopeYear(Builder $query, $year)
    {
        return $query->whereYear('created_at', $year);
    }
}
