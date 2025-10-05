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
        Schema::table("unit_route_template", function(Blueprint $table) {
            $table->string('created_by')->references('id')->on('users')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table->dropColumn('created_by');
    }
};
