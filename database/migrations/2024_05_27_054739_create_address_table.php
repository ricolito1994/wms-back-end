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
        Schema::create('address', function (Blueprint $table) {
            $table->id();
            $table->string('street')->nullable();
            $table->string('address_type')->nullable();
            $table->string('lot_number')->nullable();
            $table->integer('street_id')->references('id')->on('address')->nullable();
            $table->integer('purok_id')->references('id')->on('purok')->nullable();
            $table->integer('barangay_id')->references('id')->on('barangay')->nullable();
            $table->float('population')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->integer('created_by')->references('id')->on('users')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address');
    }
};
