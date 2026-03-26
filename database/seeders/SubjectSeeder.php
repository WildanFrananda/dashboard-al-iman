<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        Subject::create([
            'subject_code' => 'MTK-01',
            'subject_name' => 'Matematika',
            'category' => 'Wajib',
            'is_active' => true,
        ]);

        Subject::create([
            'subject_code' => 'BIN-01',
            'subject_name' => 'Bahasa Indonesia',
            'category' => 'Wajib',
            'is_active' => true,
        ]);

        Subject::create([
            'subject_code' => 'IPA-01',
            'subject_name' => 'Ilmu Pengetahuan Alam',
            'category' => 'Wajib',
            'is_active' => true,
        ]);

        Subject::create([
            'subject_code' => 'SND-01',
            'subject_name' => 'Bahasa Sunda',
            'category' => 'Muatan Lokal',
            'is_active' => true,
        ]);

        Subject::create([
            'subject_code' => 'PKS-01',
            'subject_name' => 'Pramuka',
            'category' => 'Ekstrakurikuler',
            'is_active' => true,
        ]);

        Subject::create([
            'subject_code' => 'SIL-01',
            'subject_name' => 'Pencak Silat',
            'category' => 'Ekstrakurikuler',
            'is_active' => false,
        ]);
    }
}
