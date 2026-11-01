<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ActivityLogViewerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_activity_log_viewer_route_is_removed(): void
    {
        $this->get('/admin/activity-logs')->assertNotFound();
    }
}
