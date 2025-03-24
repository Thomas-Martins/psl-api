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
        Schema::table('stores', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });

        Schema::table('carriers', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        Schema::table('carriers', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
