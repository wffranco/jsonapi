<?php

namespace Tests\Feature;

use App\Http\Middleware\ValidateJsonApiDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ValidateJsonApiDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Route::any('/test/api/request', fn () => 'OK')->middleware(ValidateJsonApiDocument::class);
    }

    public function test_all_required_fields_are_validated(): void
    {
        $this->postJson('/test/api/request', [])
            ->assertJsonValidationErrorFor('data')
            ->assertJsonValidationErrorFor('data.type')
            ->assertJsonValidationErrorFor('data.attributes');
        $this->patchJson('/test/api/request', [])
            ->assertJsonValidationErrorFor('data')
            ->assertJsonValidationErrorFor('data.type')
            ->assertJsonValidationErrorFor('data.attributes');
    }

    public function test_data_must_be_an_array(): void
    {
        $this->postJson('/test/api/request', [
            'data' => 'not-an-array',
        ])->assertJsonValidationErrorFor('data');
        $this->patchJson('/test/api/request', [
            'data' => 'not-an-array',
        ])->assertJsonValidationErrorFor('data');
    }

    public function test_data_type_is_required(): void
    {
        $this->postJson('/test/api/request', [
            'data' => [
                'type' => null,
            ],
        ])->assertJsonValidationErrorFor('data.type');
        $this->patchJson('/test/api/request', [
            'data' => [
                'type' => null,
            ],
        ])->assertJsonValidationErrorFor('data.type');

        $this->patchJson('/test/api/request', [
            'data' => [
                [
                    'type' => 'test',
                    'id' => '1',
                ],
            ],
        ])->assertSuccessful();
        $this->patchJson('/test/api/request', [
            'data' => [
                [
                    'type' => 'test',
                    'id' => null,
                ],
            ],
        ])->assertJsonValidationErrorFor('data.0.id');
    }

    public function test_data_type_must_be_a_string(): void
    {
        $this->postJson('/test/api/request', [
            'data' => [
                'type' => [],
            ],
        ])->assertJsonValidationErrorFor('data.type');
        $this->patchJson('/test/api/request', [
            'data' => [
                'type' => 0,
            ],
        ])->assertJsonValidationErrorFor('data.type');
    }

    public function test_data_attribute_must_be_an_array(): void
    {
        $this->postJson('/test/api/request', [
            'data' => [
                'attributes' => 'not-an-array',
            ],
        ])->assertJsonValidationErrorFor('data.attributes');
        $this->patchJson('/test/api/request', [
            'data' => [
                'attributes' => 'not-an-array',
            ],
        ])->assertJsonValidationErrorFor('data.attributes');
    }

    public function test_data_id_is_required_on_patch_requests(): void
    {
        $this->patchJson('/test/api/request', [])
            ->assertJsonValidationErrorFor('data.id');
    }

    public function test_data_id_must_be_a_string_on_patch_requests(): void
    {
        $this->patchJson('/test/api/request', [
            'data' => [
                'id' => [],
            ],
        ])->assertJsonValidationErrorFor('data.id');
    }

    public function test_only_accepts_valid_json_api_document(): void
    {
        $this->postJson('/test/api/request', [
            'data' => [
                'type' => 'test',
                'attributes' => [
                    'any' => 'value',
                ],
            ],
        ])->assertSuccessful();
        $this->patchJson('/test/api/request', [
            'data' => [
                'id' => '1',
                'type' => 'test',
                'attributes' => [
                    'any' => 'value',
                ],
            ],
        ])->assertSuccessful();
    }
}
