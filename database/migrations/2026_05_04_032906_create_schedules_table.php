<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transit_route_id')->constrained()->onDelete('cascade');
            $table->time('departure_time');
            $table->time('arrival_time')->nullable();
            $table->json('days_of_week')->nullable(); // ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"]
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
