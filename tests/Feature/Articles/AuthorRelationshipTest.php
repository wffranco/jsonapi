<?php

namespace Tests\Feature\Articles;

use App\Http\Resources\AuthorResource;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_the_associated_author_identifier()
    {
        $article = Article::factory()->createOne();

        $this->getJsonApi(route('api.v1.articles.relationships.author', $article))
            ->assertExactJson(AuthorResource::getIdentifier($article->author));
    }

    public function test_can_fetch_the_associated_author_resource()
    {
        $article = Article::factory()->createOne();

        $this->getJsonApi(route('api.v1.articles.author', $article))
            ->assertExactJson(AuthorResource::getResource($article->author));
    }
}
