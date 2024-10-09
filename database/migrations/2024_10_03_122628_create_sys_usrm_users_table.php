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
        Schema::create('sys_usrm_users', function (Blueprint $table) {
            $table->id()->comment('User ID');
            $table->string('username', 20)->unique()->comment('Username/Employer ID');
            $table->string('password', 255)->comment('Password');
            $table->string('phone_number', 20)->nullable()->comment('Phone Number');
            $table->string('otp_code', 6)->nullable()->comment('OTP Code');
            $table->timestamp('otp_expiry')->nullable()->comment('OTP Expiry');
            $table->string('email_address', 255)->unique()->nullable()->comment('Email Adrress');
            $table->string('name', 255)->nullable()->comment('Name');
            $table->string('ticket', 255)->nullable()->comment('Ticket/Token');
            $table->integer('telegram_id')->nullable()->comment('Telegram ID');
            $table->unsignedBigInteger('default_organisation')->comment('Default Organisation');
            $table->unsignedBigInteger('group')->comment('User Group');
            $table->boolean('is_active')->default('1')->comment('Mark is User Active');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_usrm_users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
