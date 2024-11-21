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
        Schema::create('env_tmsv_sinvs', function (Blueprint $table) {
            $table->id();
            $table->string('PN', 50)->nullable()->comment('WD Part Number');
            $table->string('WO', 50)->nullable()->comment('WD Work Order');
            $table->string('PO_NO', 50)->nullable()->comment('WD PO NO');
            $table->integer('QTY', 50)->nullable()->comment('WD PO Quantity');
            $table->string('WD_To_JV_Price', 50)->nullable()->comment('WD to JV Price');
            $table->string('WD_To_JV_Total_Quotation', 50)->nullable()->comment('WD to JV Price Quotation');
            $table->string('TransactionNo', 50)->nullable()->comment('Transaction Number');
            $table->integer('Complete_QTY', 50)->nullable()->comment('Complete Quantity');
            $table->date('Complete_Date', 50)->nullable()->comment('Complete Date');
            $table->string('Location', 50)->nullable()->comment('Location');
            $table->string('JV_To_SMTT Price', 50)->nullable()->comment('JV to SMTT Price');
            $table->string('JV_To_SMTT_Total_Quotation', 50)->nullable()->comment('JV to SMTT Price Quotation');
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('env_tmsv_sinvs');
    }
};
