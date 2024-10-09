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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->unique()->comment('Warehouse Name');
            $table->timestamp('created_at')->nullable()->comment('Created Time');
            $table->string('created_by',200)->nullable()->comment('Created By');
            $table->timestamp('updated_at')->nullable()->comment('Updated Time');
            $table->string('updated_by',200)->nullable()->comment('Updated By');
            $table->boolean('is_deleted')->default(0)->comment('Deleted Mark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
