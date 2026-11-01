<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AuthorApplication;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class BackendUiContractTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_sidebar_receives_pending_counts_on_a_child_page(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        Post::factory()->count(2)->pendingReview()->create();

        $response = $this->actingAs($admin)->get(route('admin.categories.index'));

        $response->assertOk()->assertSeeTextInOrder(['Duyệt bài', '2']);
    }

    public function test_dashboard_layout_composer_exposes_pending_author_application_count(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        AuthorApplication::factory()->count(2)->create();
        AuthorApplication::factory()->rejected()->create();
        $view = view('layouts.dashboard', [
            'title' => 'Kiểm tra',
            'errors' => new ViewErrorBag,
        ]);

        $this->actingAs($admin);
        $view->render();

        $this->assertSame(2, $view->getData()['pending_applications_count']);
        $this->assertSame(2, $view->getData()['adminStats']['pending_applications']);
    }

    public function test_admin_category_form_receives_breadcrumb_contract(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $category = Category::factory()->create();
        $this->actingAs($admin)->get(route('admin.categories.edit', $category))
            ->assertViewHas('breadcrumbs', [
                ['label' => 'Danh mục', 'url' => route('admin.categories.index')],
                ['label' => 'Sửa chuyên mục', 'url' => null],
            ]);
    }

    public function test_author_post_forms_receive_breadcrumb_contracts(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->create(['author_id' => $author]);

        $this->actingAs($author)->get(route('author.posts.create'))
            ->assertViewHas('breadcrumbs', [
                ['label' => 'Bài viết của tôi', 'url' => route('author.posts.index')],
                ['label' => 'Viết bài', 'url' => null],
            ]);

        $this->actingAs($author)->get(route('author.posts.edit', $post))
            ->assertViewHas('breadcrumbs', [
                ['label' => 'Bài viết của tôi', 'url' => route('author.posts.index')],
                ['label' => 'Sửa bài viết', 'url' => null],
            ]);
    }
}
