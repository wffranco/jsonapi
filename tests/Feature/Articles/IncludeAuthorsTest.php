<?php

namespace Tests\Feature\Articles;

use App\JsonApi\JsonApiDocument;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\ArticleController
 */
class IncludeAuthorsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_include_related_author_of_an_article()
    {
        /** @var Article $article */
        $article = Article::factory()->create();

        $this->getJsonApi(route('api.v1.articles.show', [
            'article' => $article,
            'include' => 'author',
        ]))
            ->assertOk()
            ->assertJson([
                'data' => [],
                'included' => [
                    JsonApiDocument::make($article->author)->attributes()->get('data'),
                ],
            ]);
    }

    public function test_can_include_related_authors_of_multiple_articles()
    {
        /** @var Article $article */
        $articles = Article::factory()->count(3)->create();

        $this->getJsonApi(route('api.v1.articles.index', [
            'include' => 'author',
        ]))
            ->assertOk()
            ->assertJson([
                'data' => [],
                'included' => User::all()
                    ->map(fn (User $author) => JsonApiDocument::make($author)->attributes()->get('data'))
                    ->all(),
            ]);
    }
}
