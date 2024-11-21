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
        Schema::create('wms_ship_headers', function (Blueprint $table) {
            $table->id()->comment('Shipment Header ID');
            $table->string('shipment_no',20)->unique()->comment('Shipment Number');
            $table->unsignedBigInteger('warehouse_id')->nullable()->comment('Warehouse ID');
            $table->string('bill',200)->nullable()->comment('Bill To');
            $table->string('ship',255)->nullable()->comment('Ship To');
            $table->string('customs_slip', 50)->nullable()->comment('Customs Slip');
            $table->string('customs_slip_file', 80)->nullable()->comment('Customs Slip File');
            $table->string('shipment_slip', 50)->nullable()->comment('Shipment Slip');
            $table->string('shipment_slip_file', 80)->nullable()->comment('Shipment Slip File');
            $table->string('invoice', 30)->nullable()->comment('Invoice');
            $table->string('invoice_file', 80)->nullable()->comment('Invoice File');
            $table->date('shipment_date')->nullable()->comment('Shipment Date');
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wms_ship_headers');
    }
};
