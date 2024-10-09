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
        Schema::create('modules', function (Blueprint $table) {
            $table->id()->comment('Module ID');
            $table->string('name',255)->comment('Module Name');
            $table->string('code', 8)->unique()->comment('Modules Table Code');
            $table->string('description',255)->comment('Module Description');
            $table->string('icon',100)->comment('Module Icon');
            $table->string('route', 255)->comment('Module Route');
            $table->integer('mobile')->default(0)->comment('Mobile Compatibility');
            $table->unsignedBigInteger('group')->comment('Module Group');
            $table->commonFields();
            $table->boolean('is_active')->default(1)->comment('Module Status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
