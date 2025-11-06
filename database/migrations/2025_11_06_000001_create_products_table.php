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
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['product', 'service', 'part'])->default('product');
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('cost', 10, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->integer('reorder_point')->nullable();
            $table->integer('reorder_quantity')->nullable();
            $table->string('unit')->default('pcs'); // pcs, kg, m, etc.
            $table->string('category')->nullable();
            $table->string('supplier')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('track_inventory')->default(true);
            $table->timestamps();

            $table->index(['team_id', 'sku']);
            $table->index(['team_id', 'type']);
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
