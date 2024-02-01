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
        Schema::create('cms_module_fields', function (Blueprint $table) {
            $table->id();
            $table->string('colname', 30);
            $table->string('label', 100);
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('field_type_id');
            $table->boolean('unique')->default(false);
            $table->string('default')->nullable();
            $table->unsignedInteger('minlength')->nullable();
            $table->unsignedInteger('maxlength')->nullable();
            $table->boolean('required')->default(false);
            $table->text('popup_vals')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('listing_col')->default(true);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('module_id')
                ->references('id')->on('cms_modules')
                ->onDelete('cascade');
                
            $table->foreign('field_type_id')
                ->references('id')->on('cms_module_field_types')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_module_fields');
    }
};
