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
        $this->assertDatabaseCount('categories', 15);
        $this->assertDatabaseCount('posts', 15);

        $technology = Category::query()->where('slug', 'cong-nghe')->firstOrFail();
        $business = Category::query()->where('slug', 'kinh-doanh')->firstOrFail();
        $life = Category::query()->where('slug', 'doi-song')->firstOrFail();

        $this->assertSame(7, $technology->children()->count());
        $this->assertTrue($business->children()->where('slug', 'khoi-nghiep-cong-nghe')->exists());
        $this->assertSame(4, $life->children()->count());
    }
}
