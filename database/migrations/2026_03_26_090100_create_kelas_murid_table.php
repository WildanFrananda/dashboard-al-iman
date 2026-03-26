<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kelas_murid', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('murid_id')->constrained('profil_murid')->cascadeOnDelete();
            $table->string('tahun_ajaran');
            $table->timestamps();

            $table->unique(['kelas_id', 'murid_id', 'tahun_ajaran']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('kelas_murid');
    }
};
