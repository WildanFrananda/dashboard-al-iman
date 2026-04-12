<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value')->default('');
            $table->string('label')->default('');
            $table->timestamps();
        });

        // Seed default values
        DB::table('school_settings')->insert([
            ['key' => 'jumlah_ekskul',   'value' => '0',  'label' => 'Jumlah Ekstrakurikuler', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tingkat_akreditasi', 'value' => '-', 'label' => 'Tingkat Akreditasi',      'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void {
        Schema::dropIfExists('school_settings');
    }
};
