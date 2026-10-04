<?php

namespace Tests\Feature\Admin;

use App\Enums\CommentReportAction;
use App\Enums\CommentReportStatus;
use App\Enums\CommentStatus;
use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CommentReportManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_non_admin_cannot_access_comment_report_management(): void
    {
        $report = CommentReport::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.comment-reports.index'))->assertForbidden();
        $this->actingAs($user)->put(route('admin.comment-reports.update', $report), [
            'action' => CommentReportAction::Hide->value,
        ])->assertForbidden();
    }

    public function test_admin_can_view_pending_reports_with_escaped_user_content(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $comment = Comment::factory()->create(['content' => '<script>alert("comment")</script>']);
        CommentReport::factory()->for($comment)->create([
            'description' => '<script>alert("report")</script>',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.comment-reports.index'));

        $response
            ->assertSee('&lt;script&gt;alert(&quot;comment&quot;)&lt;/script&gt;', false)
            ->assertSee('&lt;script&gt;alert(&quot;report&quot;)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert("comment")</script>', false)
            ->assertDontSee('<script>alert("report")</script>', false);
    }

    public function test_admin_can_hide_comment_and_resolve_all_of_its_pending_reports(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $comment = Comment::factory()->create();
        $firstReport = CommentReport::factory()->for($comment)->create();
        CommentReport::factory()->for($comment)->create();

        $response = $this->actingAs($admin)->put(route('admin.comment-reports.update', $firstReport), [
            'action' => CommentReportAction::Hide->value,
        ]);

        $response->assertRedirect()->assertSessionHas('status', 'Đã xử lý báo cáo bình luận.');
        $this->assertSame(CommentStatus::Hidden, $comment->refresh()->status);
        $this->assertDatabaseMissing('comment_reports', [
            'comment_id' => $comment->id,
            'status' => CommentReportStatus::Pending->value,
        ]);
        $this->assertDatabaseCount('activity_logs', 1);
    }

    public function test_dismissed_report_keeps_comment_visible_and_cannot_be_handled_twice(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $report = CommentReport::factory()->create();

        $this->actingAs($admin)->put(route('admin.comment-reports.update', $report), [
            'action' => CommentReportAction::Dismiss->value,
        ])->assertRedirect();

        $this->assertSame(CommentStatus::Visible, $report->comment->refresh()->status);
        $this->assertSame(CommentReportStatus::Dismissed, $report->refresh()->status);

        $this->actingAs($admin)->put(route('admin.comment-reports.update', $report), [
            'action' => CommentReportAction::Delete->value,
        ])->assertRedirect()->assertSessionHasErrors([
            'action' => 'Báo cáo này đã được xử lý.',
        ]);
        $this->assertNotSoftDeleted($report->comment);
    }

    public function test_admin_can_soft_delete_reported_comment(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $report = CommentReport::factory()->create();
        $comment = $report->comment;

        $this->actingAs($admin)->put(route('admin.comment-reports.update', $report), [
            'action' => CommentReportAction::Delete->value,
        ])->assertRedirect();

        $this->assertSoftDeleted($comment);
        $this->assertDatabaseHas('comment_reports', [
            'id' => $report->id,
            'status' => CommentReportStatus::Resolved->value,
            'handled_by' => $admin->id,
        ]);
    }
}
