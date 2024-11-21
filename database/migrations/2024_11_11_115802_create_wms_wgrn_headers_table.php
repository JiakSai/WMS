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
        Schema::create('wms_wgrn_headers', function (Blueprint $table) {
            $table->id()->comment('GRN Header ID');
            $table->string('receipt',20)->unique()->comment('Receipt ID');
            $table->unsignedBigInteger('warehouse_id')->nullable()->comment('Warehouse ID');
            $table->string('bill',200)->nullable()->comment('Bill To');
            $table->string('ship',255)->nullable()->comment('Ship To');
            $table->string('packing_slip', 50)->nullable()->comment('Packing Slip');
            $table->string('packing_slip_file', 80)->nullable()->comment('Packing Slip File');
            $table->string('do', 50)->nullable()->comment('Do');
            $table->string('do_file', 80)->nullable()->comment('DO File');
            $table->string('invoice', 30)->nullable()->comment('Invoice');
            $table->string('invoice_file', 80)->nullable()->comment('Invoice File');
            $table->date('receipt_date')->nullable()->comment('Receipt Date');
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wms_wgrn_headers');
    }
};
