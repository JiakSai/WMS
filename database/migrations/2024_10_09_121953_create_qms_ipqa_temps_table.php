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
        Schema::create('qms_ipqa_temps', function (Blueprint $table) {
            $table->id();
            
            // Add common fields (assuming commonFields() method exists and adds commonly used fields)
            $table->commonFields();
        
            // Add the new fields
            $table->date('date_upload');                        // Date field for date_upload
            $table->string('version_name', 255)->unique();      // String field for version_name
            $table->string('file_name', 255);                   // String field for file_name
            $table->string('folder_key', 255)->unique()->comment('Folder Location');
            $table->string('status', 255);                      // String field for status
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qms_ipqa_temps');
    }
};
