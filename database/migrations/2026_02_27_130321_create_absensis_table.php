<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertemuan_kelas_id')->constrained('pertemuan_kelas')->cascadeOnDelete();
            $table->foreignId('murid_id')->constrained('profil_murid')->cascadeOnDelete();
            $table->enum('status_kehadiran', ['Hadir', 'Izin', 'Sakit', 'Alpa']);
            $table->timestamp('waktu_absen')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
