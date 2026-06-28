<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_id');
            $table->string('locale')->index();
            $table->string('title', 120);
            $table->string('slug', 160);
            $table->string('icon')->nullable();
            $table->string('excerpt', 255)->nullable();
            $table->mediumText('content')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->boolean('meta_noindex')->default(false);
            $table->integer('status')->default(0);
            $table->timestamps();

            $table->unique(['service_id', 'locale']);
            $table->unique(['locale', 'slug']);
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_translations');
    }
};
