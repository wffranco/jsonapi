<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\ArticleController
 */
class PaginateArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_paginate_articles()
    {
        $articles = Article::factory()->count(5)->create();

        $response = $this->getJsonApi(route('api.v1.articles.index', [
            'page' => ['size' => 2, 'number' => 2],
        ]))
            ->assertJsonCount(2, 'data')
            ->assertJsonApiCollectionStructure(['title', 'content', 'slug'])
            ->assertSee([
                $articles[2]->title,
                $articles[3]->title,
            ])
            ->assertDontSee([
                $articles[0]->title,
                $articles[1]->title,
                $articles[4]->title,
            ]);

        $this->assertJsonApiPaginationLinks(
            response: $response,
            number: 2,
            size: 2,
            last: 3,
        );
    }

    public function test_can_paginate_sorted_articles()
    {
        Article::factory()->count(5)->create();
        $articles = Article::orderBy('title')->get();

        $response = $this->getJsonApi(route('api.v1.articles.index', [
            'sort' => 'title',
            'page' => ['size' => 2, 'number' => 2],
        ]))
            ->assertJsonCount(2, 'data')
            ->assertJsonApiCollectionStructure(['title', 'content', 'slug'])
            ->assertSee([
                $articles[2]->title,
                $articles[3]->title,
            ])
            ->assertDontSee([
                $articles[0]->title,
                $articles[1]->title,
                $articles[4]->title,
            ]);

        $this->assertJsonApiPaginationLinks(
            response: $response,
            queries: ['sort=title'],
            number: 2,
            size: 2,
            last: 3,
        );
    }

    public function test_can_paginate_filtered_articles()
    {
        Article::factory()->create(['title' => 'Title 1']);
        Article::factory()->create(['title' => 'Title 3']);
        Article::factory()->create(['title' => 'Title 5']);
        Article::factory()->count(2)->create(['title' => 'Something else']);
        Article::factory()->create(['title' => 'Title 2']);
        Article::factory()->create(['title' => 'Title 4']);
        Article::factory()->create(['title' => 'Title 6']);

        $response = $this->getJsonApi(route('api.v1.articles.index', [
            'filter[title]' => 'Title',
            'page' => ['size' => 2, 'number' => 2],
        ]))
            ->assertJsonCount(2, 'data')
            ->assertJsonApiCollectionStructure(['title', 'content', 'slug'])
            ->assertSee([
                'Title 2',
                'Title 5',
            ])
            ->assertDontSee([
                'Title 1',
                'Title 3',
                'Title 4',
                'Title 6',
                'Something else',
            ]);

        $this->assertJsonApiPaginationLinks(
            response: $response,
            queries: ['filter[title]=Title'],
            number: 2,
            size: 2,
            last: 3,
        );
    }
}
