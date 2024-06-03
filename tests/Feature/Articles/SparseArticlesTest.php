<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SparseArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_fields_can_be_sparsed_in_articles_index()
    {
        $article = Article::factory()->create();

        $this->getJsonApi(route('api.v1.articles.index', [
            'fields' => ['articles' => 'title,slug'],
        ]))
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'type',
                        'id',
                        'attributes' => ['title', 'slug'],
                        'links' => ['self'],
                    ],
                ],
            ])
            ->assertJsonFragment([
                'title' => $article->title,
                'slug' => $article->slug,
            ])
            ->assertJsonMissing([
                'content' => $article->content,
            ])
            ->assertJsonMissing([
                'content' => null,
            ]);
    }

    public function test_route_key_is_always_included_in_articles_index()
    {
        $article = Article::factory()->create();

        $this->getJsonApi(route('api.v1.articles.index', [
            'fields' => ['articles' => 'title'],
        ]))
            ->assertJsonFragment([
                'id' => (string) $article->getRouteKey(),
            ])
            ->assertJsonFragment([
                'title' => $article->title,
            ])
            ->assertJsonMissing([
                'content' => $article->content,
                'slug' => $article->slug,
            ])
            ->assertJsonMissing([
                'content' => null,
                'slug' => null,
            ]);
    }
}
