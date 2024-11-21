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
        Schema::create('mrb_lvlc_galls', function (Blueprint $table) {
            $table->id();
            $table->integer("group_id");
            $table->integer("username");
            $table->integer("level_id");
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mrb_lvlc_galls');
    }
};
