<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('post_translations', function (Blueprint $table) {
            // When set and in the future, the post is "scheduled" and hidden from
            // the front; posts:publish-due flips due ones to published and clears it.
            $table->timestamp('scheduled_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('post_translations', function (Blueprint $table) {
            $table->dropColumn('scheduled_at');
        });
    }
};
