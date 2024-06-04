<?php

namespace Tests\Unit\JsonApi;

use App\JsonApi\JsonApiDocument;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function test_can_create_a_json_api_document(): void
    {
        $document = JsonApiDocument::make()
            ->type('posts')
            ->id('1')
            ->attributes([
                'title' => 'Title',
                'content' => 'Content',
                'slug' => 'slug',
            ])
            ->all();

        $expected = [
            'data' => [
                'type' => 'posts',
                'id' => '1',
                'attributes' => [
                    'title' => 'Title',
                    'content' => 'Content',
                    'slug' => 'slug',
                ],
            ],
        ];

        $this->assertEquals(
            actual: $document,
            expected: $expected,
        );
    }
}
