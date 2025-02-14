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
        Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('email')->unique()->nullable(false);
            $table->string('phone',20)->nullable(false);
            $table->string('address',255)->nullable(false);
            $table->string('zipcode', 5)->nullable(false);
            $table->string('city',100)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carriers');
    }
};
