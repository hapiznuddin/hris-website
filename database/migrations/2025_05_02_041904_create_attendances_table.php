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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('clock_in')->nullable(); // Waktu masuk
            $table->time('clock_out')->nullable(); // Waktu pulang
            $table->enum('status', ['Hadir', 'Terlambat', 'Pulang Cepat', 'Alpha', 'Izin'])->default('Alpha');
            $table->text('reason')->nullable(); // Alasan jika izin
            // $table->string('location')->nullable(); // Lokasi (jika pakai GPS)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
