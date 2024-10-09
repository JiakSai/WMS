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
        Schema::create('qms_ipqa_psmts', function (Blueprint $table) {
            $table->id();
            $table->date('date');                      // Column for date
            $table->string('wo');                      // Column for wo (work order)
            $table->string('customer');                // Column for customer
            $table->string('line');                    // Column for line
            $table->string('model');                   // Column for model
            $table->string('cus_wo');                  // Column for cus_wo (customer work order)
            $table->string('file_name');               // Column for file_name
            $table->string('step');                    // Column for step
            $table->string('shift');                   // Column for shift
            $table->date('download_date');             // Column for download_date
            $table->date('submit_date');               // Column for submit_date
            $table->string('verify_card_id');          // Column for verify_card_id
            $table->date('verify_date');               // Column for verify_date
            $table->string('card_id');                 // Column for card_id
            $table->unsignedBigInteger('template_id'); // Column for template_id
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qms_ipqa_psmts');
    }
};
