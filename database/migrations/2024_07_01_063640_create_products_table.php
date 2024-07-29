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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('SKU', 35)->unique();
            $table->text('description')->nullable();
            $table->float('price')->unsigned()->default(1);
            $table->tinyInteger('discount')->nullable();
            $table->unsignedSmallInteger('quantity')->default(0);
            $table->tinyText('thumbnail'); // main image
            $table->timestamps();
            $table->fullText(['slug']);
            $table->fullText(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropFullText(['name']);
            $table->dropFullText(['slug']);
        });

        Schema::dropIfExists('products');
    }
};
