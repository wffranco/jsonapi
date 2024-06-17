<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\ArticleController
 */
class DeleteArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_delete_articles(): void
    {
        $article = Article::factory()->create();

        $this->assertDatabaseCount('articles', 1);

        $this->deleteJsonApi(route('api.v1.articles.update', $article))
            ->assertUnauthorized();
        // ->assertJsonApiError('Unauthenticated.', 401);

        $this->assertDatabaseCount('articles', 1);
    }

    public function test_can_delete_owned_article(): void
    {
        $article = Article::factory()->create();

        $this->assertDatabaseCount('articles', 1);

        $this->actingAs($article->author)
            ->deleteJsonApi(route('api.v1.articles.destroy', $article))
            ->assertNoContent();

        $this->assertDatabaseCount('articles', 0);
    }
}
