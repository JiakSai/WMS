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
        Schema::create('sys_usrm_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('Role Name');
            $table->json('permissions')->nullable()->comment('Role Permissions');
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_usrm_roles');
    }
};
