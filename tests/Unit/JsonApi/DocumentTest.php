<?php

namespace Tests\Unit\JsonApi;

use App\JsonApi\Http\Resources\Json\Document;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function test_can_create_a_document_from_data(): void
    {
        $document = Document::make()
            ->type('users')
            ->id('1')
            ->attributes([
                'name' => 'Name',
                'alias' => 'alias',
            ])
            ->relationshipData([
                'role' => [
                    'data' => [
                        'type' => 'roles',
                        'id' => '1',
                    ],
                ],
            ])
            ->all();
        $expected = [
            'data' => [
                'type' => 'users',
                'id' => '1',
                'attributes' => [
                    'name' => 'Name',
                    'alias' => 'alias',
                ],
                'relationships' => [
                    'role' => [
                        'data' => [
                            'type' => 'roles',
                            'id' => '1',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals(
            actual: $document,
            expected: $expected,
        );
    }

    public function test_can_create_a_document_for_a_model(): void
    {
        /** @var Model $role */
        $role = $this->getModel([
            'type' => 'roles',
            'id' => '1',
        ]);
        /** @var Model $user */
        $user = $this->getModel([
            'type' => 'users',
            'id' => '1',
            'name' => 'Name',
            'alias' => 'alias',
        ], function (MockInterface $mock) use ($role) {
            $mock->shouldReceive('getRelations')->andReturn([
                'role' => $role,
            ]);
        });

        $document = Document::make($user)
            ->attributes()
            ->relationshipData([
                'role' => $role,
            ])
            ->all();
        $expected = [
            'data' => [
                'type' => $user->getResourceType(),
                'id' => $user->getRouteKey(),
                'attributes' => $user->getAttributes(),
                'relationships' => [
                    'role' => [
                        'data' => [
                            'type' => $role->getResourceType(),
                            'id' => $role->getRouteKey(),
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals(
            actual: $document,
            expected: $expected,
        );
    }

    public function test_can_create_a_document_list_from_data(): void
    {
        $document = Document::make()->put('data', [
            Document::make()
                ->type('users')
                ->id('1')
                ->attributes([
                    'name' => 'Name',
                    'alias' => 'alias',
                ])
                ->relationshipData([
                    'role' => [
                        'data' => [
                            'type' => 'roles',
                            'id' => '1',
                        ],
                    ],
                ])
                ->get('data'),
            Document::make()
                ->type('users')
                ->id('2')
                ->attributes([
                    'name' => 'Name',
                    'alias' => 'alias',
                ])
                ->relationshipData([
                    'role' => [
                        'data' => [
                            'type' => 'roles',
                            'id' => '1',
                        ],
                    ],
                ])
                ->get('data'),
        ])->all();
        $expected = [
            'data' => [
                [
                    'type' => 'users',
                    'id' => '1',
                    'attributes' => [
                        'name' => 'Name',
                        'alias' => 'alias',
                    ],
                    'relationships' => [
                        'role' => [
                            'data' => [
                                'type' => 'roles',
                                'id' => '1',
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'users',
                    'id' => '2',
                    'attributes' => [
                        'name' => 'Name',
                        'alias' => 'alias',
                    ],
                    'relationships' => [
                        'role' => [
                            'data' => [
                                'type' => 'roles',
                                'id' => '1',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals(
            actual: $document,
            expected: $expected,
        );
    }

    public function test_can_create_a_document_for_a_collection_of_models(): void
    {
        /** @var Model $role */
        $role = $this->getModel([
            'type' => 'roles',
            'id' => '1',
        ]);
        /** @var Model $user */
        $user = $this->getModel([
            'type' => 'users',
            'id' => '1',
            'name' => 'Name',
            'alias' => 'alias',
        ], function (MockInterface $mock) use ($role) {
            $mock->shouldReceive('getRelations')->andReturn([
                'role' => $role,
            ]);
        });
        $users = collect([$user]);

        $document = Document::make(
            $users,
            fn ($item) => $item->attributes()->relationshipData()
        )->all();
        $expected = [
            'data' => [
                [
                    'type' => $user->getResourceType(),
                    'id' => $user->getRouteKey(),
                    'attributes' => $user->getAttributes(),
                    'relationships' => [
                        'role' => [
                            'data' => [
                                'type' => $role->getResourceType(),
                                'id' => $role->getRouteKey(),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals(
            actual: $document,
            expected: $expected,
        );
    }

    private function getModel(array $data, ?Closure $closure = null)
    {
        $id = $data['id'];
        $type = $data['type'];
        $attributes = Arr::except($data, ['id', 'type']);

        return Mockery::mock(Model::class, function (MockInterface $mock) use ($id, $type, $attributes, $closure) {
            $mock->shouldReceive('getResourceType')->andReturn($type);
            $mock->shouldReceive('getRouteKey')->andReturn($id);
            $mock->shouldReceive('getVisible')->andReturn([]);
            $mock->shouldReceive('getHidden')->andReturn([]);
            $mock->shouldReceive('getFillable')->andReturn(array_keys($attributes));
            $mock->shouldReceive('getAttributes')->andReturn($attributes);
            $mock->shouldReceive('only')->andReturn($attributes);

            $mock->shouldReceive('setAttribute')->andReturnSelf();
            $mock->shouldReceive('offsetExists')->andReturn(false);
            if ($closure) {
                $closure($mock);
            }
        });
    }
}
