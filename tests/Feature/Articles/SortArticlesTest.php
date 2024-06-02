<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\ArticleController
 */
class SortArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_sort_articles_by_title_asc(): void
    {
        $titles = ['A Title', 'B Title', 'C Title'];
        Article::factory()->create(['title' => $titles[2]]);
        Article::factory()->create(['title' => $titles[0]]);
        Article::factory()->create(['title' => $titles[1]]);

        $this->getJsonApi(route('api.v1.articles.index', ['sort' => 'title']))
            ->assertOk()
            ->assertSeeInOrder($titles);
    }

    public function test_can_sort_articles_by_title_desc(): void
    {
        $titles = ['C Title', 'B Title', 'A Title'];
        Article::factory()->create(['title' => $titles[2]]);
        Article::factory()->create(['title' => $titles[0]]);
        Article::factory()->create(['title' => $titles[1]]);

        $this->getJsonApi(route('api.v1.articles.index', ['sort' => '-title']))
            ->assertOk()
            ->assertSeeInOrder($titles);
    }

    public function test_can_sort_articles_by_content_asc(): void
    {
        $contents = ['A Content', 'B Content', 'C Content'];
        Article::factory()->create(['content' => $contents[2]]);
        Article::factory()->create(['content' => $contents[0]]);
        Article::factory()->create(['content' => $contents[1]]);

        $this->getJsonApi(route('api.v1.articles.index', ['sort' => 'content']))
            ->assertOk()
            ->assertSeeInOrder($contents);
    }

    public function test_can_sort_articles_by_content_desc(): void
    {
        $contents = ['C Content', 'B Content', 'A Content'];
        Article::factory()->create(['content' => $contents[2]]);
        Article::factory()->create(['content' => $contents[0]]);
        Article::factory()->create(['content' => $contents[1]]);

        $this->getJsonApi(route('api.v1.articles.index', ['sort' => '-content']))
            ->assertOk()
            ->assertSeeInOrder($contents);
    }
}
