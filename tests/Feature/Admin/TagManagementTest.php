<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TagManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_tag_management_route_is_removed(): void
    {
        $this->get('/admin/tags')->assertNotFound();
    }
}
