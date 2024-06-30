<?php

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $article_id
 * @property string $user_id
 * @property string $body
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Article $article
 * @property-read User $author
 *
 * @method static CommentFactory<static> factory($count = null, $state = [])
 * @method static static newModelQuery()
 * @method static static newQuery()
 * @method static static query()
 * @method static static whereArticleId($value)
 * @method static static whereBody($value)
 * @method static static whereCreatedAt($value)
 * @method static static whereId($value)
 * @method static static whereUpdatedAt($value)
 * @method static static whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'user_id',
        'body',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
