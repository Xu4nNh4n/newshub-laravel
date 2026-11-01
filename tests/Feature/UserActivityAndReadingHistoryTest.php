<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class UserActivityAndReadingHistoryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_reading_and_activity_history_routes_are_removed(): void
    {
        $this->get('/reading-history')->assertNotFound();
        $this->get('/activity-history')->assertNotFound();
    }
}
