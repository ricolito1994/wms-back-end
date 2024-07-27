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
        Schema::create('unit_route', function (Blueprint $table) {
            $table->id();
            $table->integer('unit_id')->references('id')->on('units')->nullable();
            $table->integer('address_id')->references('id')->on('address')->nullable();
            $table->integer('created_by')->references('id')->on('users')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_route');
    }
};
