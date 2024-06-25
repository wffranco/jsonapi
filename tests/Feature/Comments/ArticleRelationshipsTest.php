<?php

namespace Tests\Feature\Comments;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_the_associated_article_identifier()
    {
        $comment = Comment::factory()->createOne();

        $this->getJsonApi(route('api.v1.comments.relationships.article', $comment))
            ->assertExactJson(ArticleResource::getIdentifier($comment->article));
    }

    public function test_can_fetch_the_associated_article_resource()
    {
        $comment = Comment::factory()->createOne();

        $this->getJsonApi(route('api.v1.comments.article', $comment))
            ->assertExactJson(ArticleResource::getResource($comment->article));
    }

    public function test_can_update_the_associated_article()
    {
        $article = Article::factory()->createOne();
        $comment = Comment::factory()->createOne();

        $this->actingAs($comment->author)
            ->patchJsonApi(
                route('api.v1.comments.relationships.article', $comment),
                ArticleResource::getIdentifier($article),
            )
            ->assertExactJson(ArticleResource::getResource($article))
            ->assertOk();

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'article_id' => $article->id,
        ]);
    }

    public function test_article_must_exists_in_database_when_updating()
    {
        $comment = Comment::factory()->createOne();

        $this->actingAs($comment->author)
            ->patchJsonApi(
                route('api.v1.comments.relationships.article', $comment),
                ['type' => 'articles', 'id' => '999'],
            )
            ->assertJsonApiValidationErrors('data.id');
    }
}
