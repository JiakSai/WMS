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
        Schema::create('wms_rfid_mgmts', function (Blueprint $table) {
            $table->id()->comment('RFID ID');
            $table->string('name', 50)->unique()->comment('RFID CODE');
            $table->unsignedInteger('group')->nullable()->comment('RFID GROUP (group for rfid code that created in same time)');
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wms_rfid_mgmts');
    }
};
