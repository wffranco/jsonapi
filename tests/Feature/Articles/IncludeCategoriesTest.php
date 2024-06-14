<?php

namespace Tests\Feature\Articles;

use App\JsonApi\JsonApiDocument;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\ArticleController
 */
class IncludeCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_include_related_category_of_an_article()
    {
        /** @var Article $article */
        $article = Article::factory()->create();

        $this->getJsonApi(route('api.v1.articles.show', [
            'article' => $article,
            'include' => 'category',
        ]))
            ->assertOk()
            ->assertJson([
                'data' => [],
                'included' => [
                    JsonApiDocument::make($article->category)->attributes()->get('data'),
                ],
            ]);
    }
}
