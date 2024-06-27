<?php

namespace Tests\Feature\Articles;

use App\Http\Resources\CommentResource;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentsRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_the_associated_comments_identifiers()
    {
        $this->withoutExceptionHandling();
        $article = Article::factory()->hasComments(2)->createOne();
        $this->getJsonApi(route('api.v1.articles.relationships.comments', $article))
            ->assertJsonCount(2, 'data')
            ->assertExactJson(['data' => CommentResource::getIdentifiers($article->comments)]);
    }

    public function test_returns_an_empty_data_array_when_the_article_has_no_comments()
    {
        $article = Article::factory()->createOne();
        $this->getJsonApi(route('api.v1.articles.relationships.comments', $article))
            ->assertJsonCount(0, 'data')
            ->assertExactJson(['data' => []]);
    }

    public function test_can_fetch_the_associated_comments_resources()
    {
        $article = Article::factory()->hasComments(2)->createOne();
        $this->getJsonApi(route('api.v1.articles.comments', $article))
            ->assertJsonCount(2, 'data')
            ->assertJson([
                'data' => CommentResource::getCollectionResources($article->comments),
            ]);
    }

    public function test_returns_an_empty_data_array_when_the_article_has_no_comments_for_the_comments_resources()
    {
        $article = Article::factory()->createOne();
        $this->getJsonApi(route('api.v1.articles.comments', $article))
            ->assertJsonCount(0, 'data')
            ->assertJson(['data' => []]);
    }

    public function test_can_update_the_associated_comments()
    {
        $article = Article::factory()->createOne();

        $author = User::factory()->create();
        $comments = Comment::factory(2)->create(['user_id' => $author->id]);

        $this->actingAs($author, ['comment:update'])
            ->patchJsonApi(
                route('api.v1.articles.relationships.comments', $article),
                ['data' => CommentResource::getIdentifiers($comments)],
            )
            ->assertJsonCount(2, 'data')
            ->assertJson(['data' => CommentResource::getIdentifiers($comments)]);

        $article->refresh();
        $this->assertTrue($article->comments->contains($comments[0]));
        $this->assertTrue($article->comments->contains($comments[1]));
    }
}
