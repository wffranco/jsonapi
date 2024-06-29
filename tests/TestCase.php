<?php

namespace Tests;

use App\JsonApi\Sanctum\Testing\CanAuthenticate;
use App\JsonApi\Testing\TestCaseHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CanAuthenticate, TestCaseHandler;
}
