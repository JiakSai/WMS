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
        Schema::create('main_modules', function (Blueprint $table) {
            $table->id()->comment('Main Module ID');
            $table->string('name',255)->comment('Main Module Name');
            $table->string('code', 3)->unique()->comment('Main Modules Table Code');
            $table->string('icon',100)->comment('Main Modules Icon');
            $table->commonFields();
            $table->boolean('is_active')->default(1)->comment('Module Status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_modules');
    }
};
