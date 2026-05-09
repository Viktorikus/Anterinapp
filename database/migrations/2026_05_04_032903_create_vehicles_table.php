<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trayek_code', 20);
            $table->string('trayek_name');
            $table->enum('type', ['bus', 'angkot', 'kereta', 'transjakarta'])->default('angkot');
            $table->enum('status', ['berangkat', 'berhenti', 'menuju_halte'])->default('berhenti');
            $table->integer('capacity')->default(20);
            $table->string('plate_number', 20)->nullable();
            $table->string('driver_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
