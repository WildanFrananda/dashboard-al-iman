<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('academic_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('category', ['libur', 'ujian', 'kegiatan', 'umum'])->default('umum');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#0F609B')->comment('Hex color e.g. #EF4444');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('academic_events');
    }
};
