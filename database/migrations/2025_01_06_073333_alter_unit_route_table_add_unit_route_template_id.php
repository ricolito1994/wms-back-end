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
        
        Schema::table('unit_route', function (Blueprint $table) {
            $table->integer('unit_route_template_id')->references('id')->on('unit_route_template')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_route', function (Blueprint $table) { 
            $table->dropColumn('unit_route_template_id');
        });
    }
};
