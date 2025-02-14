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
            $table->string('name', 100)->nullable(false);
            $table->text('description')->nullable();
            $table->string('reference')->unique()->nullable(false);
            $table->string('location')->unique()->nullable(false);
            $table->unsignedInteger('stock')->default(0)->nullable(false);
            $table->decimal('price', 8, 2)->nullable(false);
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
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
