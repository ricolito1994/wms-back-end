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
        Schema::create('waste_collection', function (Blueprint $table) {
            $table->id();
            $table->float('net_weight');
            $table->string('waste_type')->references('id')->on('waste_type')->nullable();
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
        Schema::dropIfExists('waste_collection');
    }
};
