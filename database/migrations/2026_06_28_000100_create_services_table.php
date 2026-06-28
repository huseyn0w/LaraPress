<?php // database/migrations/2026_06_28_000100_create_services_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('services'); }
};
