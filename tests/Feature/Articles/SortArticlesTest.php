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

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['C', 'A', 'B'] as $t) {
            foreach (['B', 'A', 'C'] as $c) {
                $item = ['title' => "$t$c Title", 'content' => "$c$t Content"];
                Article::factory()->create($item);
            }
        }
    }

    public function test_can_sort_articles_by_title(): void
    {
        $titles = Article::orderBy('title')->select('title')->pluck('title')->toArray();

        // Sort by title ascending
        $this->getJsonApi(route('api.v1.articles.index', ['sort' => 'title']))
            ->assertOk()
            ->assertSeeInOrder($titles);
        // Sort by title descending
        $this->getJsonApi(route('api.v1.articles.index', ['sort' => '-title']))
            ->assertOk()
            ->assertSeeInOrder(array_reverse($titles));
    }

    public function test_can_sort_articles_by_content(): void
    {
        $contents = Article::orderBy('content')->select('content')->pluck('content')->toArray();

        // Sort by content ascending
        $this->getJsonApi(route('api.v1.articles.index', ['sort' => 'content']))
            ->assertOk()
            ->assertSeeInOrder($contents);
        // Sort by content descending
        $this->getJsonApi(route('api.v1.articles.index', ['sort' => '-content']))
            ->assertOk()
            ->assertSeeInOrder(array_reverse($contents));
    }

    public function test_can_sort_articles_by_title_and_content(): void
    {
        $items = Article::all(['title', 'content'])->map(fn ($item) => $item->only('title', 'content'));

        // Alternate sort orders for title and content
        $rules = [['asc', ''], ['desc', '-']];
        foreach ($rules as [$to, $th]) {
            foreach ($rules as [$co, $ch]) {
                $sortedItems = $items->sortBy([['title', $to], ['content', $co]]);
                $this->getJsonApi(route('api.v1.articles.index', ['sort' => "{$th}title,{$ch}content"]))
                    ->assertOk()
                    ->assertSeeInOrder($sortedItems->pluck('title')->toArray())
                    ->assertSeeInOrder($sortedItems->pluck('content')->toArray());
            }
        }
    }

    public function test_cannot_sort_articles_by_unknown_field(): void
    {
        Article::factory()->create();

        $this->getJsonApi(route('api.v1.articles.index', ['sort' => 'unknown']))
            ->assertJsonApiError(
                title: 'Bad Request',
                detail: "Invalid sort fields in 'articles' resource: unknown.",
                status: 400,
            );
    }
}
