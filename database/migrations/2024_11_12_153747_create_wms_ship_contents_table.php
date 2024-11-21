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
        Schema::create('wms_ship_contents', function (Blueprint $table) {
            $table->id()->comment('Shipment Content ID');
            $table->unsignedBigInteger('ship_header_id')->comment('Shipment Header ID');
            $table->string('serial_number', 30)->unique()->comment('Serial Number');
            $table->string('item_code', 50)->comment('Item Code');
            $table->string('mpn', 50)->nullable()->comment('Manufacturer Part Number');
            $table->string('location', 50)->nullable()->comment('Warehouse Location');
            $table->string('lot')->nullable()->comment('Lot Number');
            $table->date('manufacture_date')->nullable()->comment('Manufacture Date');
            $table->integer('quantity')->nullable()->comment('Quantity');
            $table->string('uom', 20)->nullable()->comment('Unit of Measurement');
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wms_ship_contents');
    }
};
