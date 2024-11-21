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
        Schema::create('mrb_emrb_contents', function (Blueprint $table) {
            $table->id();
            $table->string("form_id", 20);
            $table->string('part_number');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('currency')->nullable();
            $table->text('defect')->nullable();
            $table->text('disposition')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('correction')->nullable();
            $table->text('remark')->nullable();
            $table->string('file_path')->nullable();
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mrb_emrb_contents');
    }
};
