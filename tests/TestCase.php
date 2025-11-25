<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Database\Seeders\IndonesiaMinimalSeeder;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure minimal Indonesia location data exists for validation rules
        $this->seed(IndonesiaMinimalSeeder::class);
    }
}
