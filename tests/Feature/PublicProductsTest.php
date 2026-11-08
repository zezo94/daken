<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_example(): void
    {
        $this->get('/')
            ->assertOk();
    }
}
