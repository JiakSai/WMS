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
        Schema::create('wms_item_grpcs_childs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->comment('Item Groups Id');
            $table->string('name',255)->comment('Item Grouping Selection Option Name');
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wms_item_grpcs_childs');
    }
};
