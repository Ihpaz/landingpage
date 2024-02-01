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
        Schema::create('user_address', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('regency_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('village_id')->nullable();
            $table->text('address');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('country_id')
                ->references('id')->on('m_country')
                ->onDelete('restrict');

            $table->foreign('province_id')
                ->references('id')->on('m_province')
                ->onDelete('restrict');

            $table->foreign('regency_id')
                ->references('id')->on('m_regency')
                ->onDelete('restrict');

            $table->foreign('district_id')
                ->references('id')->on('m_district')
                ->onDelete('restrict');

            $table->foreign('village_id')
                ->references('id')->on('m_village')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_address');
    }
};
