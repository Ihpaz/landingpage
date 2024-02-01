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
        Schema::create('m_country_currency', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('currency_id');

            $table->foreign('country_id')
                ->references('id')->on('m_country')
                ->onDelete('cascade');

            $table->foreign('currency_id')
                ->references('id')->on('m_currency')
                ->onDelete('cascade');
                
            $table->primary(['country_id','currency_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_country_currency');
    }
};
