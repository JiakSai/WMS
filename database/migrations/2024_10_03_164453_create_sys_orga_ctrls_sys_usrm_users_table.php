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
        Schema::create('sys_orga_ctrls_sys_usrm_users', function (Blueprint $table) {
            $table->unsignedBigInteger('sys_usrm_users_id')->comment('Pivot Table User ID');
            $table->unsignedBigInteger('sys_orga_ctrls_id')->comment('Pivot Table Organization ID');
            $table->timestamps();
    
            $table->primary(['sys_usrm_users_id', 'sys_orga_ctrls_id']);
            $table->foreign('sys_usrm_users_id')->references('id')->on('sys_usrm_users')->onDelete('cascade');
            $table->foreign('sys_orga_ctrls_id')->references('id')->on('sys_orga_ctrls')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_orga_ctrls_sys_usrm_users');
    }
};
