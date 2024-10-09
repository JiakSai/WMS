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
        Schema::create('sys_modu_subms', function (Blueprint $table) {
            $table->id()->comment('Sub Module ID');
            $table->string('name',255)->comment('Sub Module Name');
            $table->string('code', 15)->unique()->comment('Sub Modules Table Code');
            $table->unsignedBigInteger('group')->comment('Sub Module Group');
            $table->string('description',255)->comment('Sub Module Description');
            $table->string('icon',100)->comment('Sub Module Icon');
            $table->string('route', 255)->comment('Sub Module Route');
            $table->integer('mobile')->default(0)->comment('Sub Mobile Compatibility');
            $table->commonFields();
            $table->boolean('is_active')->default(1)->comment('Sub Module Status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_modu_subms');
    }
};
