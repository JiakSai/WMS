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
        Schema::create('wms_item_datas', function (Blueprint $table) {
            $table->id();
            $table->string('name',255)->comment('Item Name');
            $table->string('description',255)->nullable()->comment('Item Description');
            $table->string('type',50)->nullable()->comment('Item Type');
            $table->string('unit_set',20)->nullable()->comment('Item Unit Set');
            $table->string('inventory_unit',20)->nullable()->comment('Item Inventory Unit');
            $table->string('weight_unit',20)->nullable()->comment('Item Weight Unit');
            $table->decimal('weight', 8, 4)->nullable()->comment('Item Weight Unit');
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wms_item_datas');
    }
};
