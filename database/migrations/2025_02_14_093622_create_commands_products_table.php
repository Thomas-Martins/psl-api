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
        Schema::create('commands_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('command_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('freeze_price', 8, 2);
            $table->timestamps();


            //Index for faster queries
            $table->index('command_id');
            $table->index('product_id');

            //Unique constraint
            $table->unique(['command_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commands_products');
    }
};
