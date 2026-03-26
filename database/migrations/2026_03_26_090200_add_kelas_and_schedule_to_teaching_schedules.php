<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('teaching_schedules', function (Blueprint $table) {
            $table->foreignId('kelas_id')->nullable()->after('subject_id')->constrained('kelas')->cascadeOnDelete();
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'])->nullable()->after('kelas_id');
            $table->time('jam_mulai')->nullable()->after('hari');
            $table->time('jam_selesai')->nullable()->after('jam_mulai');
        });
    }

    public function down(): void {
        Schema::table('teaching_schedules', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn(['kelas_id', 'hari', 'jam_mulai', 'jam_selesai']);
        });
    }
};
