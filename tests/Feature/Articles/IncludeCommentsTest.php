<?php

namespace Tests\Feature\Articles;

use App\Http\Resources\CommentResource;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncludeCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_include_related_comments_of_an_article()
    {
        $article = Article::factory()->hasComments(2)->create();

        $this->getJsonApi(route('api.v1.articles.show', [
            'article' => $article,
            'include' => 'comments',
        ]))
            ->assertOk()
            ->assertJsonCount(2, 'included')
            ->assertJson([
                'included' => CommentResource::getCollectionResources($article->comments),
            ]);
    }
}
