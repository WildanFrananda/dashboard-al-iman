<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('murid_id')->constrained('profil_murid')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('profil_guru')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->enum('tipe_nilai', ['UTS', 'UAS']);
            $table->smallInteger('nilai')->default(0)->comment('0–100');
            $table->tinyInteger('semester')->comment('1 atau 2');
            $table->string('tahun_ajaran', 9)->comment('Format: YYYY/YYYY');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Satu murid hanya boleh punya satu nilai UTS dan satu UAS
            // per mata pelajaran per kelas per semester per tahun ajaran.
            $table->unique(
                ['murid_id', 'subject_id', 'kelas_id', 'tipe_nilai', 'semester', 'tahun_ajaran'],
                'nilais_unique_per_murid_subject'
            );
        });
    }

    public function down(): void {
        Schema::dropIfExists('nilais');
    }
};
