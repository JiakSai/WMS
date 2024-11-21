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
        Schema::create('mrb_emrb_headers', function (Blueprint $table) {
            $table->string('mbr_number', 20)->primary(); // Unique ID in the format MRBGTRYYMM####
            $table->string('cost_born_by', 50)->nullable();
            $table->string('transfer_no', 50)->nullable();
            $table->date('date_submit')->nullable();
            $table->string('mes_kitlist', 50)->nullable();
            $table->string('plant', 50)->nullable();
            $table->string('line', 50)->nullable();
            $table->string('model', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('initiator', 50)->nullable();
            $table->string('department', 50)->nullable();
            $table->string('organisation_name', 50)->nullable();
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mrb_emrb_headers');
    }
};
