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
        Schema::create('wms_role_asgns', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('Role Name');
            $table->json('user_id')->nullable()->comment('User Id');
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wms_role_asgns');
    }
};
