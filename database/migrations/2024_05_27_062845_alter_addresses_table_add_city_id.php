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
        //
        Schema::table('purok', function (Blueprint $table) { 
            $table->integer('city_id')->references('id')->on('city')->nullable();
        });
        Schema::table('barangay', function (Blueprint $table) { 
            $table->integer('city_id')->references('id')->on('city')->nullable();
        });
        Schema::table('address', function (Blueprint $table) { 
            $table->integer('city_id')->references('id')->on('city')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('purok', function (Blueprint $table) { 
            $table->dropColumn('city_id');
        });
        Schema::table('barangay', function (Blueprint $table) { 
            $table->dropColumn('city_id');
        });
        Schema::table('address', function (Blueprint $table) { 
            $table->dropColumn('city_id');
        });
    }
};
