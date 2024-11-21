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
        Schema::create('sys_role_auths', function (Blueprint $table) {
            $table->id();
            $table->string('type',15)->comment('Permission Type');
            $table->unsignedBigInteger('main_module')->comment('Main Module Id');
            $table->string('name',50)->comment('Permission Name');
            $table->string('description',200)->comment('Permission Description');
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_role_auths');
    }
};
