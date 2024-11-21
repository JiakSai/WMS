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
        Schema::create('qms_role_asgns', function (Blueprint $table) {
            $table->id();
            $table->string('role_id',255)->comment('Item Grouping Name');
            $table->string('username',255)->comment('Item Grouping Name');
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qms_role_asgns');
    }
};
