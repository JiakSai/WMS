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
        Schema::create('wms_invt_ctrls', function (Blueprint $table) {
            $table->id()->comment('Inventory ID');
            $table->string('item_code',100)->comment('Item Code');
            $table->unsignedBigInteger('warehouse_id')->nullable()->comment('Warehosue Id');
            $table->unsignedBigInteger('warehouse_location_id')->nullable()->comment('Warehouse Location Id');
            $table->string('lot')->nullable()->comment('Lot Number');
            $table->integer('quantity')->nullable()->comment('Item Quantity');
            $table->string('uom', 20)->nullable()->comment('Unit of Measurement');
            $table->dateTime('manufacture_date')->nullable()->comment('Manufacture Date');
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wms_invt_ctrls');
    }
};
