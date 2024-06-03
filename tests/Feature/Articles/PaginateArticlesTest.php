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
        $url = route('api.v1.articles.index', [
            'page' => ['size' => 2, 'number' => 2],
        ]);

        $response = $this->getJsonApi($url);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'type',
                    'id',
                    'attributes' => ['title', 'content', 'slug'],
                    'links' => ['self'],
                ],
            ],
            'links' => ['first', 'last', 'prev', 'next'],
        ]);
        $response->assertSee([
            $articles[2]->title,
            $articles[3]->title,
        ]);
        $response->assertDontSee([
            $articles[0]->title,
            $articles[1]->title,
            $articles[4]->title,
        ]);

        $links = [
            'first' => ['page[number]=1', 'page[size]=2'],
            'last' => ['page[number]=3', 'page[size]=2'],
            'prev' => ['page[number]=1', 'page[size]=2'],
            'next' => ['page[number]=3', 'page[size]=2'],
        ];
        foreach ($links as $name => $validations) {
            $link = urldecode($response->json("links.{$name}"));
            foreach ($validations as $validation) {
                $this->assertStringContainsString($validation, $link);
            }
        }
    }

    public function test_can_paginate_sorted_articles()
    {
        Article::factory()->count(5)->create();
        $articles = Article::orderBy('title')->get();
        $url = route('api.v1.articles.index', [
            'sort' => 'title',
            'page' => ['size' => 2, 'number' => 2],
        ]);

        $response = $this->getJsonApi($url);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'type',
                    'id',
                    'attributes' => ['title', 'content', 'slug'],
                    'links' => ['self'],
                ],
            ],
            'links' => ['first', 'last', 'prev', 'next'],
        ]);
        $response->assertSee([
            $articles[2]->title,
            $articles[3]->title,
        ]);
        $response->assertDontSee([
            $articles[0]->title,
            $articles[1]->title,
            $articles[4]->title,
        ]);

        $links = [
            'first' => ['page[number]=1', 'page[size]=2', 'sort=title'],
            'last' => ['page[number]=3', 'page[size]=2', 'sort=title'],
            'prev' => ['page[number]=1', 'page[size]=2', 'sort=title'],
            'next' => ['page[number]=3', 'page[size]=2', 'sort=title'],
        ];
        foreach ($links as $name => $validations) {
            $link = urldecode($response->json("links.{$name}"));
            foreach ($validations as $validation) {
                $this->assertStringContainsString($validation, $link);
            }
        }
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
        $url = route('api.v1.articles.index', [
            'filter[title]' => 'Title',
            'page' => ['size' => 2, 'number' => 2],
        ]);

        $response = $this->getJsonApi($url);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'type',
                    'id',
                    'attributes' => ['title', 'content', 'slug'],
                    'links' => ['self'],
                ],
            ],
            'links' => ['first', 'last', 'prev', 'next'],
        ]);
        $response->assertSee([
            'Title 2',
            'Title 5',
        ]);
        $response->assertDontSee([
            'Title 1',
            'Title 3',
            'Title 4',
            'Title 6',
            'Something else',
        ]);

        $links = [
            'first' => ['page[number]=1', 'page[size]=2', 'filter[title]=Title'],
            'last' => ['page[number]=3', 'page[size]=2', 'filter[title]=Title'],
            'prev' => ['page[number]=1', 'page[size]=2', 'filter[title]=Title'],
            'next' => ['page[number]=3', 'page[size]=2', 'filter[title]=Title'],
        ];
        foreach ($links as $name => $validations) {
            $link = urldecode($response->json("links.{$name}"));
            foreach ($validations as $validation) {
                $this->assertStringContainsString($validation, $link);
            }
        }
    }
}
