<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
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
        Article::factory()->count(2)->create(['created_at' => now()->month(5)]);
        $article = Article::factory()->create(['created_at' => now()->month(10)]);

        $this->getJsonApi(route('api.v1.articles.index', [
            'filter' => ['month' => 10],
        ]))
            ->assertJsonCount(1, 'data')
            ->assertSee($article->title);
    }

    public function test_can_filter_articles_by_day()
    {
        Article::factory()->count(2)->create(['created_at' => now()->day(10)]);
        $article = Article::factory()->create(['created_at' => now()->day(20)]);

        $this->getJsonApi(route('api.v1.articles.index', [
            'filter' => ['day' => 20],
        ]))
            ->assertJsonCount(1, 'data')
            ->assertSee($article->title);
    }

    public function test_cannot_filter_articles_by_unknown_field()
    {
        $this->getJsonApi(route('api.v1.articles.index', [
            'filter' => ['unknown' => 'value'],
        ]))
            ->assertStatus(400)
            ->assertSee('filter.unknown');
    }
}
