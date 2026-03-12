<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pertemuan_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_schedule_id')->constrained('teaching_schedules')->cascadeOnDelete();
            $table->date('tanggal_pertemuan');
            $table->string('materi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pertemuan_kelas');
    }
};
