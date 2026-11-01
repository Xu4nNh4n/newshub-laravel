<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CommentReportManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_comment_report_routes_are_removed(): void
    {
        $this->get('/admin/comment-reports')->assertNotFound();
        $this->post('/comments/1/reports')->assertNotFound();
    }
}
