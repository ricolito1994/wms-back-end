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
        Schema::create('unit', function (Blueprint $table) {
            $table->id();
            $table->string('model_name');
            $table->string('plate_number');
            $table->string('chassis_number');
            $table->float('gross_weight');
            $table->string('gps_tracking_unique_id')->nullable();
            $table->string('alias')->nullable();
            $table->string('assigned_to')->references('id')->on('users')->nullable();
            $table->string('created_by')->references('id')->on('users')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit');
    }
};
