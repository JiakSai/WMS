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
        Schema::create('mrb_lvlc_rolls', function (Blueprint $table) {
            $table->id();
            $table->string("level");
            $table->string("descriptions");
            $table->integer("max_users");
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mrb_lvlc_rolls');
    }
};
