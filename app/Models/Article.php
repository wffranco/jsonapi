<?php

namespace App\Models;

use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $category_id
 * @property string $user_id
 * @property string $title
 * @property string $content
 * @property string $slug
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $author
 * @property-read Category $category
 * @property-read Collection<Comment> $comments
 * @property-read int|null $comments_count
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
 * @method static Builder|self author(string $authors) Author filter
 * @method static Builder|self category(string $categories) Category filter
 * @method static Builder|self comments(string $comments) Comments filter
 * @method static Builder|self day($day)
 * @method static Builder|self month($month)
 * @method static Builder|self year($year)
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
        'category_id',
        'user_id',
        'title',
        'content',
        'slug',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /** Author relationship */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Category relationship */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Comments relationship */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function scopeAuthor(Builder $query, string $authors)
    {
        return $query->whereHas('author',
            fn (Builder $query) => $query->whereIn('alias', explode(',', $authors)),
        );
    }

    public function scopeCategory(Builder|self $query, string $categories)
    {
        return $query->whereHas('category',
            fn (Builder|Category $query) => $query->whereIn('slug', explode(',', $categories)),
        );
    }

    public function scopeComments(Builder|self $query, $comments)
    {
        return $query->whereHas('comments',
            fn (Builder|self $query) => $query->whereIn('id', explode(',', $comments)),
        );
    }

    public function scopeDay(Builder|self $query, $day)
    {
        return $query->whereDay('created_at', $day);
    }

    public function scopeMonth(Builder|self $query, $month)
    {
        return $query->whereMonth('created_at', $month);
    }

    public function scopeYear(Builder|self $query, $year)
    {
        return $query->whereYear('created_at', $year);
    }
}
