<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\ArticleController
 */
class FilterArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_filter_articles_by_title()
    {
        Article::factory()->count(2)->create(['title' => 'A Laravel title']);
        $article = Article::factory()->create(['title' => 'A PHP title']);

        $this->getJsonApi(route('api.v1.articles.index', [
            'filter' => ['title' => 'Laravel'],
        ]))
            ->assertJsonCount(2, 'data')
            ->assertDontSee($article->title);
    }

    public function test_can_filter_articles_by_content()
    {
        Article::factory()->count(2)->create(['content' => 'A Laravel content']);
        $article = Article::factory()->create(['content' => 'A PHP content']);

        $this->getJsonApi(route('api.v1.articles.index', [
            'filter' => ['content' => 'Laravel'],
        ]))
            ->assertJsonCount(2, 'data')
            ->assertDontSee($article->title);
    }

    public function test_can_filter_articles_by_year()
    {
        Article::factory()->count(2)->create(['created_at' => now()->year(2010)]);
        $article = Article::factory()->create(['created_at' => now()->year(2020)]);

        $this->getJsonApi(route('api.v1.articles.index', [
            'filter' => ['year' => 2020],
        ]))
            ->assertJsonCount(1, 'data')
            ->assertSee($article->title);
    }

    public function test_can_filter_articles_by_month()
    {
        Article::factory()->count(2)->create(['created_at' => now()->subYear()->month(5)]);
        $article = Article::factory()->create(['created_at' => now()->subYear()->month(10)]);

        $this->getJsonApi(route('api.v1.articles.index', [
            'filter' => ['month' => 10],
        ]))
            ->assertJsonCount(1, 'data')
            ->assertSee($article->title);
    }

    public function test_can_filter_articles_by_day()
    {
        Article::factory()->count(2)->create(['created_at' => now()->subMonth()->day(10)]);
        $article = Article::factory()->create(['created_at' => now()->subMonth()->day(20)]);

        $this->getJsonApi(route('api.v1.articles.index', [
            'filter' => ['day' => 20],
        ]))
            ->assertJsonCount(1, 'data')
            ->assertSee($article->title);
    }

    public function test_can_filter_articles_by_categories()
    {
        $articles = Article::factory()->count(2)->create();
        $category1 = Category::factory()->hasArticles(2)->create(['slug' => 'cat-1']);
        $category2 = Category::factory()->hasArticles(1)->create(['slug' => 'cat-2']);

        // Filter by one category.
        $this->getJsonApi(route('api.v1.articles.index', [
            'filter' => ['category' => 'cat-1'],
        ]))
            ->assertJsonCount(2, 'data')
            ->assertSee($category1->articles[0]->title)
            ->assertSee($category1->articles[1]->title)
            ->assertDontSee($category2->articles[0]->title)
            ->assertDontSee($articles[0]->title)
            ->assertDontSee($articles[1]->title);

        // Filter by multiple categories.
        $this->getJsonApi(route('api.v1.articles.index', [
            'filter' => ['category' => 'cat-1,cat-2'],
        ]))
            ->assertJsonCount(3, 'data')
            ->assertSee($category1->articles[0]->title)
            ->assertSee($category1->articles[1]->title)
            ->assertSee($category2->articles[0]->title)
            ->assertDontSee($articles[0]->title)
            ->assertDontSee($articles[1]->title);
    }

    public function test_cannot_filter_articles_by_unknown_field()
    {
        $this->getJsonApi(route('api.v1.articles.index', [
            'filter' => ['unknown' => 'value'],
        ]))
            ->assertStatus(400)
            ->assertJsonApiError(
                title: 'Bad Request',
                detail: "Filter not allowed in 'articles' resource: unknown.",
                status: 400,
            );
    }
}
