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
        Schema::create('wms_item_datas_wms_item_grpcs_childs', function (Blueprint $table) {

            $table->unsignedBigInteger('wms_item_datas_id')->comment('Pivot Table Item Data Id');
            $table->unsignedBigInteger('wms_item_grpcs_childs_id')->comment('Pivot Table Item Grouping Child Id');
            $table->timestamps();

            $table->primary(['wms_item_datas_id' , 'wms_item_grpcs_childs_id']);
            $table->foreign('wms_item_datas_id', 'fk_item_datas')
                  ->references('id')->on('wms_item_datas')->onDelete('cascade');
            $table->foreign('wms_item_grpcs_childs_id', 'fk_grpcs_childs')
                  ->references('id')->on('wms_item_grpcs_childs')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wms_item_datas_wms_item_grpcs_childs');
    }
};
