<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['id' => 1, 'code' => 'user', 'name' => 'Độc giả', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'code' => 'author', 'name' => 'Tác giả', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'code' => 'admin', 'name' => 'Quản trị viên', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('role_id')->default(1)->constrained('roles');
        });

        foreach (['user' => 1, 'author' => 2, 'admin' => 3] as $role => $roleId) {
            DB::table('users')->where('role', $role)->update(['role_id' => $roleId]);
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex('users_role_index');
            $table->dropColumn(['role', 'avatar']);
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn('description');
        });

        Schema::table('posts', function (Blueprint $table): void {
            $table->dropIndex('posts_is_featured_index');
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'show_thumbnail_in_post',
                'is_featured',
            ]);
        });

        Schema::table('comments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('reply_to_id');
        });

        Schema::table('author_applications', function (Blueprint $table): void {
            $table->string('cv_path')->nullable()->after('bio');
            $table->dropColumn(['sample_title', 'sample_content', 'updated_at']);
        });

        Schema::table('post_requests', function (Blueprint $table): void {
            $table->dropColumn('priority');
        });

        Schema::dropIfExists('notifications');
    }

    public function down(): void
    {
        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::table('post_requests', function (Blueprint $table): void {
            $table->string('priority')->default('normal');
        });

        Schema::table('author_applications', function (Blueprint $table): void {
            $table->string('sample_title')->nullable();
            $table->text('sample_content')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->dropColumn('cv_path');
        });

        Schema::table('comments', function (Blueprint $table): void {
            $table->foreignId('reply_to_id')->nullable()->constrained('comments')->nullOnDelete();
        });

        Schema::table('posts', function (Blueprint $table): void {
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->boolean('show_thumbnail_in_post')->default(true);
            $table->boolean('is_featured')->default(false)->index();
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->text('description')->nullable();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('user')->index();
            $table->string('avatar')->nullable();
        });

        foreach ([1 => 'user', 2 => 'author', 3 => 'admin'] as $roleId => $role) {
            DB::table('users')->where('role_id', $roleId)->update(['role' => $role]);
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('role_id');
        });

        Schema::dropIfExists('roles');
    }
};
