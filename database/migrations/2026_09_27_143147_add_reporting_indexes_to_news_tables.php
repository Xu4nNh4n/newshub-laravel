<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->index('view_count', 'posts_view_count_index');
        });

        Schema::table('activity_logs', function (Blueprint $table): void {
            $table->index(['action', 'created_at'], 'activity_logs_action_created_at_index');
            $table->index(['user_id', 'created_at'], 'activity_logs_user_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table): void {
            $table->dropIndex('activity_logs_action_created_at_index');
            $table->dropIndex('activity_logs_user_created_at_index');
        });

        Schema::table('posts', function (Blueprint $table): void {
            $table->dropIndex('posts_view_count_index');
        });
    }
};
