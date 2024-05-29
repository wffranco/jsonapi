<?php

namespace Tests;

use App\JsonApi\Tests\JsonApiRequests;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use JsonApiRequests;
}
