<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TagManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_non_admin_cannot_manage_tags(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.tags.index'))->assertForbidden();
    }

    public function test_admin_can_view_tags_index_with_modals(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $tag = Tag::factory()->create(['name' => 'Công nghệ']);

        $this->actingAs($admin)
            ->get(route('admin.tags.index'))
            ->assertOk()
            ->assertSee('Thẻ')
            ->assertSee('Thêm thẻ')
            ->assertSee('#Công nghệ')
            ->assertSee('id="tag-form-modal"', false)
            ->assertSee('id="tag-delete-modal"', false);
    }

    public function test_admin_can_create_and_update_tag(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $createResponse = $this->actingAs($admin)->post(route('admin.tags.store'), [
            'name' => 'Trí tuệ nhân tạo',
            'slug' => '',
        ]);
        $tag = Tag::query()->firstOrFail();
        $updateResponse = $this->put(route('admin.tags.update', $tag), [
            'name' => 'AI',
            'slug' => 'Artificial Intelligence',
        ]);

        $createResponse->assertRedirect(route('admin.tags.index'));
        $updateResponse->assertRedirect(route('admin.tags.index'));
        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => 'AI', 'slug' => 'artificial-intelligence']);
    }

    public function test_deleting_tag_removes_pivot_but_keeps_post(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $tag = Tag::factory()->create();
        $post = Post::factory()->create();
        $post->tags()->attach($tag);

        $response = $this->actingAs($admin)->delete(route('admin.tags.destroy', $tag));

        $response->assertRedirect(route('admin.tags.index'));
        $this->assertModelMissing($tag);
        $this->assertModelExists($post);
        $this->assertDatabaseMissing('post_tag', ['tag_id' => $tag->id, 'post_id' => $post->id]);
    }

    public function test_tag_store_returns_created_json_when_requested(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)->postJson(route('admin.tags.store'), [
            'name' => 'OpenAI',
            'slug' => '',
        ])->assertCreated()->assertJson([
            'success' => true,
            'message' => 'Đã tạo thẻ.',
            'data' => ['name' => 'OpenAI', 'slug' => 'openai'],
        ]);

        $this->assertDatabaseHas('tags', ['name' => 'OpenAI', 'slug' => 'openai']);
    }

    public function test_tag_update_returns_json_when_requested(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $tag = Tag::factory()->create();

        $this->actingAs($admin)->putJson(route('admin.tags.update', $tag), [
            'name' => 'AI tạo sinh',
            'slug' => '',
        ])->assertOk()->assertJson([
            'success' => true,
            'message' => 'Đã cập nhật thẻ.',
            'data' => ['name' => 'AI tạo sinh', 'slug' => 'ai-tao-sinh'],
        ]);

        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'slug' => 'ai-tao-sinh']);
    }

    public function test_tag_destroy_returns_json_when_requested(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $tag = Tag::factory()->create();

        $this->actingAs($admin)
            ->deleteJson(route('admin.tags.destroy', $tag))
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Đã xóa thẻ.']);

        $this->assertModelMissing($tag);
    }
}
