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
        Schema::create('commands', function (Blueprint $table) {
            $table->id();
            $table->enum("status",["pending","processing","completed","cancelled","delivery","delivered"]);
            $table->string("reference")->unique();
            $table->date("estimated_delivery_date");
            $table->date('departure_date')->nullable();
            $table->date('arrival_date')->nullable();
            $table->decimal('total_price', 10, 2)->unsigned();
            $table->string('cancellation_reason')->nullable();
            $table->foreignId("carrier_id")->constrained()->onDelete('set null');
            $table->foreignId("user_id")->constrained()->onDelete('set null');
            $table->timestamps();

            //Indexes for faster queries
            $table->index('departure_date');
            $table->index('arrival_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commands');
    }
};
