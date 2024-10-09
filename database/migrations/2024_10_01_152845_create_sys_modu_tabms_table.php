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
        Schema::create('sys_modu_tabms', function (Blueprint $table) {
            $table->id()->comment('Tab Module ID');
            $table->string('name',255)->comment('Tab Module Name');
            $table->string('code', 20)->unique()->comment('Tab Modules Table Code');
            $table->unsignedBigInteger('main_group')->comment('Tab Module Main Group');
            $table->unsignedBigInteger('sub_group')->comment('Tab Module Sub Group');
            $table->string('route', 100)->comment('Main Module Route');
            $table->commonFields();
            $table->boolean('is_active')->default(1)->comment('Tab Module Status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_modu_tabms');
    }
};
