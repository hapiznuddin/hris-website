<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Relasi opsional ke user
            $table->string('nip')->unique();       // Nomor Induk Pegawai
            $table->string('name');                // Nama Karyawan
            $table->string('nik')->nullable();     // Nomor Induk Kependudukan
            $table->string('phone')->nullable();   // Nomor Telepon
            $table->string('gender')->nullable();  // Jenis Kelamin
            $table->string('religion')->nullable(); // Agama
            $table->string('place_of_birth')->nullable(); // Tempat Lahir
            $table->date('birth_date')->nullable(); // Tanggal Lahir
            $table->string('email')->nullable();   // Email Karyawan
            $table->string('position');            // Jabatan
            $table->string('department');          // Departemen
            $table->string('marital_status')->nullable(); // Status Perkawinan
            $table->text('address')->nullable();   // Alamat
            $table->string('photo')->nullable();   // Foto
            $table->string('url_photo')->nullable();   // Foto
            $table->string('status')->nullable();  // Status Karyawan
            $table->date('join_date');             // Tanggal Bergabung
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
