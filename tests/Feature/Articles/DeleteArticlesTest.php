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

    public function test_can_delete_article()
    {
        $article = Article::factory()->create();

        $this->assertDatabaseCount('articles', 1);

        $this->deleteJsonApi(route('api.v1.articles.destroy', $article))
            ->assertNoContent();

        $this->assertDatabaseCount('articles', 0);
    }
}
