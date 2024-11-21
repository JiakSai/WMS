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
        Schema::create('wms_whmg_whmgs', function (Blueprint $table) {
            $table->id();
            $table->string('name',255)->comment('Warehouse Name');
            $table->string('description',255)->nullable()->comment('Warehouse Description');
            $table->string('type',50)->nullable()->comment('Warehouse Type');
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wms_whmg_whmgs');
    }
};
