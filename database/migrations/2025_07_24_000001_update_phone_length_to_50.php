<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 50)->change();
        });
        Schema::table('carriers', function (Blueprint $table) {
            $table->string('phone', 50)->change();
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('phone', 50)->change();
        });
        Schema::table('stores', function (Blueprint $table) {
            $table->string('phone', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->change();
        });
        Schema::table('carriers', function (Blueprint $table) {
            $table->string('phone', 20)->change();
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('phone', 20)->change();
        });
        Schema::table('stores', function (Blueprint $table) {
            $table->string('phone', 20)->change();
        });
    }
};
