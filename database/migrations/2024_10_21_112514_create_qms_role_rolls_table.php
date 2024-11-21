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
        Schema::create('qms_role_rolls', function (Blueprint $table) {
            $table->id();
            $table->string('role_name',255)->comment('Role Name');
            $table->string('remark',255)->comment('Role Remark');
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qms_role_rolls');
    }
};
