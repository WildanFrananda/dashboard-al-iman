<?php

declare(strict_types=1);

use App\Models\ProfilMurid;
use App\Models\User;
use Illuminate\Support\Facades\DB;

describe('Student registration DB transaction', function () {
    it('creates both User and ProfilMurid in a single transaction', function () {
        DB::transaction(function () {
            $user = User::factory()->murid()->create([
                'name' => 'Test Student',
                'email' => 'student@test.com',
            ]);

            ProfilMurid::create([
                'user_id' => $user->id,
                'nis' => '1234567890',
                'nama_lengkap' => 'Test Student',
                'status' => 'aktif',
            ]);
        });

        expect(User::where('email', 'student@test.com')->exists())->toBeTrue();
        expect(ProfilMurid::where('nis', '1234567890')->exists())->toBeTrue();
    });

    it('rolls back User creation when ProfilMurid creation fails', function () {
        $countBefore = User::count();

        try {
            DB::transaction(function () {
                User::factory()->murid()->create(['email' => 'rollback@test.com']);
                // Simulate failure by inserting invalid ProfilMurid
                ProfilMurid::create([
                    'user_id' => null, // violates NOT NULL
                    'nis' => '9999999999',
                    'nama_lengkap' => 'Will Fail',
                    'status' => 'aktif',
                ]);
            });
        } catch (Exception) {
            // expected
        }

        expect(User::count())->toBe($countBefore);
    });
});
