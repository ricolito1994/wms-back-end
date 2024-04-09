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
        Schema::create('persons_involved_trip', function (Blueprint $table) {
            $table->id();
            $table->string('person')->references('id')->on('users')->nullable();
            $table->string('waste_management')->references('id')->on('waste_management')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persons_involved_trip');
    }
};
