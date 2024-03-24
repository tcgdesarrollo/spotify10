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
        Schema::create('chart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chart_date_id')->constrained()->cascadeOnDelete();
            $table->integer('position');
            $table->string('title');
            $table->string('singer')->nullable();
            $table->string('last_position')->nullable();
            $table->string('peak_position')->nullable();
            $table->string('week_on_chart')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_items');
    }
};
