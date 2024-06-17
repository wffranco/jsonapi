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

    public function test_can_include_related_categories_of_multiple_articles()
    {
        /** @var Article $article */
        $articles = Article::factory()->count(3)->create();

        $this->getJsonApi(route('api.v1.articles.index', [
            'include' => 'category',
        ]))
            ->assertOk()
            ->assertJson([
                'data' => [],
                'included' => $articles->map(
                    fn (Article $article) => JsonApiDocument::make($article->category)->attributes()->get('data')
                )->all(),
            ]);
    }

    public function test_cannot_include_unknown_relationship()
    {
        /** @var Article $article */
        $article = Article::factory()->createOne();

        $include = 'unknown,unknown2';
        $title = 'Bad Request';
        $detail = "Includes not allowed in 'articles' resource: unknown, unknown2.";
        $status = 400;

        $this->getJsonApi(route('api.v1.articles.show', [
            'article' => $article,
            'include' => $include,
        ]))
            ->assertJsonApiError($title, $detail, $status);

        $this->getJsonApi(route('api.v1.articles.index', [
            'include' => $include,
        ]))
            ->assertJsonApiError($title, $detail, $status);
    }
}
