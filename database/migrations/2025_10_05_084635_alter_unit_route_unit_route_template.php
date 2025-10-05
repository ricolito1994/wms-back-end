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
        Schema::table("unit_route", function(Blueprint $table) {
            $table->string("lat", 255)->nullable();
            $table->string("lng", 255)->nullable();
        });
        Schema::table("unit_route_template", function(Blueprint $table) {
            $table->string("route_template_name", 255)->nullable();
            $table->string("description", 255)->nullable();
            $table->string("schedule_day_of_week", 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table("unit_route", function(Blueprint $table) {
            $table->dropColumn("lat");
            $table->dropColumn("lng");
        });
        Schema::table("unit_route_template", function(Blueprint $table) {
            $table->dropColumn("route_template_name");
            $table->dropColumn("description");
            $table->dropColumn("schedule_day_of_week");
        });
    }
};
