<?php

namespace Tests\Feature\Admin;

use App\Enums\CategoryStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_non_admin_cannot_manage_categories(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.categories.index'))->assertForbidden();
    }

    public function test_admin_can_view_category_tree_index(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $root = Category::factory()->create(['name' => 'Công nghệ cha']);
        $child = Category::factory()->create(['name' => 'Trí tuệ con', 'parent_id' => $root->id]);
        $deletedParent = Category::factory()->create();
        $orphan = Category::factory()->create(['parent_id' => $deletedParent]);
        $deletedParent->delete();

        $this->actingAs($admin)
            ->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Quản lý chuyên mục')
            ->assertSee('Mở tất cả')
            ->assertSee('Thu gọn')
            ->assertSee('Công nghệ cha')
            ->assertSee('Trí tuệ con')
            ->assertSee('thuộc Công nghệ cha')
            ->assertSee('id="cat-form-modal"', false)
            ->assertSee('id="cat-delete-modal"', false)
            ->assertViewHas('parentCategories', fn ($categories): bool => $categories->contains($root))
            ->assertViewHas('orphanCategories', fn ($categories): bool => $categories->contains($orphan))
            ->assertViewHas('allCategories', fn ($categories): bool => $categories->contains($child));
    }

    public function test_admin_can_create_and_update_category_with_normalized_slug(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $parent = Category::factory()->create();

        $createResponse = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Công nghệ mới',
            'slug' => '',
            'description' => 'Tin tức công nghệ.',
            'status' => CategoryStatus::Active->value,
            'parent_id' => $parent->id,
        ]);
        $category = Category::query()->where('slug', 'cong-nghe-moi')->firstOrFail();
        $updateResponse = $this->put(route('admin.categories.update', $category), [
            'name' => 'Công nghệ',
            'slug' => 'Cong Nghe',
            'description' => null,
            'status' => CategoryStatus::Hidden->value,
            'parent_id' => null,
        ]);

        $createResponse->assertRedirect(route('admin.categories.index'));
        $updateResponse->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'slug' => 'cong-nghe',
            'status' => CategoryStatus::Hidden->value,
            'parent_id' => null,
        ]);
    }

    public function test_category_forms_receive_only_valid_parent_options(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $root = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $root]);

        $this->actingAs($admin)
            ->get(route('admin.categories.create'))
            ->assertOk()
            ->assertViewHas('parentCategories', fn ($categories): bool => $categories->modelKeys() === [$root->id]);

        $this->actingAs($admin)
            ->get(route('admin.categories.edit', $root))
            ->assertOk()
            ->assertViewHas('parentCategories', fn ($categories): bool => $categories->isEmpty());

        $this->assertSame($root->id, $child->parent->id);
        $this->assertTrue($root->children->contains($child));
    }

    public function test_category_cannot_parent_itself_or_create_a_third_level(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $root = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $root]);

        $payload = [
            'name' => $child->name,
            'slug' => $child->slug,
            'description' => $child->description,
            'status' => CategoryStatus::Active->value,
        ];

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $child), [...$payload, 'parent_id' => $child->id])
            ->assertSessionHasErrors('parent_id');

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                ...$payload,
                'name' => 'Chuyên mục cấp ba',
                'slug' => 'chuyen-muc-cap-ba',
                'parent_id' => $child->id,
            ])
            ->assertSessionHasErrors('parent_id');
    }

    public function test_admin_cannot_delete_category_that_has_posts(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $category = Category::factory()->create();
        Post::factory()->create(['category_id' => $category]);

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

        $response->assertSessionHasErrors('category', 'Không thể xóa chuyên mục đang có bài viết hoặc danh mục con.');
        $this->assertNotSoftDeleted($category);
    }

    public function test_admin_cannot_delete_category_that_has_children(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $category = Category::factory()->create();
        Category::factory()->create(['parent_id' => $category]);

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertSessionHasErrors('category');

        $this->assertNotSoftDeleted($category);
    }

    public function test_admin_can_soft_delete_empty_category(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertSoftDeleted($category);
    }

    public function test_category_store_returns_created_json_when_requested(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)->postJson(route('admin.categories.store'), [
            'name' => 'Khoa học',
            'slug' => '',
            'status' => CategoryStatus::Active->value,
        ])->assertCreated()->assertJson([
            'success' => true,
            'message' => 'Đã tạo chuyên mục.',
            'data' => ['name' => 'Khoa học', 'slug' => 'khoa-hoc'],
        ]);

        $this->assertDatabaseHas('categories', ['name' => 'Khoa học', 'slug' => 'khoa-hoc']);
    }

    public function test_category_update_returns_json_when_requested(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $category = Category::factory()->create();

        $this->actingAs($admin)->putJson(route('admin.categories.update', $category), [
            'name' => 'Kinh tế số',
            'slug' => '',
            'status' => CategoryStatus::Active->value,
        ])->assertOk()->assertJson([
            'success' => true,
            'message' => 'Đã cập nhật chuyên mục.',
            'data' => ['name' => 'Kinh tế số', 'slug' => 'kinh-te-so'],
        ]);

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'slug' => 'kinh-te-so']);
    }

    public function test_category_destroy_returns_json_and_reports_dependency_errors(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $emptyCategory = Category::factory()->create();
        $categoryWithChild = Category::factory()->create();
        Category::factory()->create(['parent_id' => $categoryWithChild]);

        $this->actingAs($admin)
            ->deleteJson(route('admin.categories.destroy', $emptyCategory))
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Đã xóa chuyên mục.']);
        $this->assertSoftDeleted($emptyCategory);

        $this->actingAs($admin)
            ->deleteJson(route('admin.categories.destroy', $categoryWithChild))
            ->assertUnprocessable()
            ->assertJson([
                'success' => false,
                'message' => 'Không thể xóa chuyên mục đang có bài viết hoặc danh mục con.',
            ]);
        $this->assertNotSoftDeleted($categoryWithChild);
    }
}
