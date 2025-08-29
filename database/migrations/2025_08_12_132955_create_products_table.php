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
            $table->string('name')->unique();
            $table->string('category_code', 100);
            $table->foreign('category_code')->references('category_code')->on('categories')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(0);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('sku')->unique()->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('type')->default('product'); // 'product' or 'service'
            $table->string('unit')->nullable(); // e.g., 'kg', 'pcs', etc.
            $table->date('mfg_date')->nullable();
            $table->date('exp_date')->nullable();
            $table->string('brand')->nullable(); // e.g., 'Brand Name'
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
