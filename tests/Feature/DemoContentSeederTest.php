<?php

namespace Tests\Feature;

use App\Models\Category;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DemoContentSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_demo_seeder_is_repeatable_without_duplicating_records(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseCount('categories', 11);
        $this->assertDatabaseCount('tags', 12);
        $this->assertDatabaseCount('posts', 15);
        $this->assertDatabaseCount('post_tag', 31);

        $technology = Category::query()->where('slug', 'cong-nghe')->firstOrFail();
        $business = Category::query()->where('slug', 'kinh-doanh')->firstOrFail();

        $this->assertSame(7, $technology->children()->count());
        $this->assertTrue($business->children()->where('slug', 'khoi-nghiep-cong-nghe')->exists());
    }
}
