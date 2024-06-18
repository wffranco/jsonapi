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
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: 401,
            );

        $this->assertDatabaseCount('articles', 1);
    }

    public function test_can_delete_owned_articles(): void
    {
        $article = Article::factory()->create();

        $this->assertDatabaseCount('articles', 1);

        $this->actingAs($article->author)
            ->deleteJsonApi(route('api.v1.articles.destroy', $article))
            ->assertNoContent();

        $this->assertDatabaseCount('articles', 0);
    }

    public function test_cannot_delete_other_users_articles(): void
    {
        $article = Article::factory()->create();

        $this->assertDatabaseCount('articles', 1);

        $this->actingAs()
            ->deleteJsonApi(route('api.v1.articles.destroy', $article))
            ->assertForbidden();

        $this->assertDatabaseCount('articles', 1);
    }
}
