<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class FavoriteManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_favorite_routes_are_removed(): void
    {
        $this->get('/favorites')->assertNotFound();
        $this->post('/news/1/favorite')->assertNotFound();
        $this->delete('/news/1/favorite')->assertNotFound();
    }
}
