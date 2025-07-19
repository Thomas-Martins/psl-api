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
            $table->string('name')->nullable(false);
            $table->string('address', 255)->nullable(false);
            $table->string('zipcode', 5)->nullable(false);
            $table->string('city', 100)->nullable(false);
            $table->string('phone',20)->nullable(false);
            $table->string('email')->unique()->nullable(false);
            $table->string('siret', 14)->unique()->nullable(false);
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
