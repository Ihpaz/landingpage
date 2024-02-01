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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('guid')->nullable();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('fullname');
            $table->string('nickname')->nullable();
            $table->string('phonenumber')->nullable();
            $table->string('company')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('nip')->nullable()->unique();
            $table->string('nik')->nullable()->unique();
            $table->string('npwp')->nullable()->unique();
            $table->string('pernr')->nullable();
            $table->string('status');
            $table->string('password');
            $table->string('avatar_url')->nullable();
            $table->text('thumbnail_photo')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('fcm_token')->nullable();
            $table->string('locale')->nullable();
            $table->string('timezone')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
