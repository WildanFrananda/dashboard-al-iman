<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('kelas', function (Blueprint $table) {
            $table->unsignedTinyInteger('level')->nullable()->after('nama_kelas');
            $table->string('kelompok', 10)->nullable()->after('level');
        });
    }

    public function down(): void {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn(['level', 'kelompok']);
        });
    }
};
