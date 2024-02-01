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
        Schema::create('cms_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('url')->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('permission')->nullable();
            $table->string('type')->default('module');
            $table->integer('parent')->unsigned()->default(0);
            $table->integer('hierarchy');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_menus');
    }
};
