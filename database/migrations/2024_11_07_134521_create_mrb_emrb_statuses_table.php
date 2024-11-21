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
        Schema::create('mrb_emrb_statuses', function (Blueprint $table) {
            $table->id();
            $table->string("form_id", 20);
            $table->string("status", 45);
            $table->integer("level");
            $table->string("remark");
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mrb_emrb_statuses');
    }
};
