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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address', 255);
            $table->string('zipcode', 5);
            $table->string('city', 100);
            $table->string('phone',20);
            $table->string('email')->unique();
            $table->string('siret', 14)->unique();
            $table->timestamps();
            $table->softDeletes();

            //Index for faster queries
            $table->index(['name', 'city']);
            $table->index('siret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
