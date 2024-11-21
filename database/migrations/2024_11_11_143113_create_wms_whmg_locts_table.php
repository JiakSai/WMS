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
        Schema::create('wms_whmg_locts', function (Blueprint $table) {
            $table->id()->comment('Location ID');
            $table->unsignedBigInteger('warehouse_id')->comment('Warehouse ID');
            $table->string('name')->comment('Warehouse Location Name');
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wms_whmg_locts');
    }
};
