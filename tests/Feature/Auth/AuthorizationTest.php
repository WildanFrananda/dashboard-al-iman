<?php

declare(strict_types=1);

use App\Models\User;

// ── Guest access ─────────────────────────────────────────────────────────────

describe('Guest access', function () {
    it('redirects guest to login when accessing dashboard', function () {
        $this->get('/dashboard')->assertRedirect('/login');
    });

    it('redirects guest to login when accessing manage-user', function () {
        $this->get('/manage-user')->assertRedirect('/login');
    });

    it('redirects guest to login when accessing manage-class', function () {
        $this->get('/manage-class')->assertRedirect('/login');
    });

    it('redirects guest to login when accessing manage-student', function () {
        $this->get('/manage-student')->assertRedirect('/login');
    });

    it('redirects guest to login when accessing teacher-attendance', function () {
        $this->get('/teacher-attendance')->assertRedirect('/login');
    });

    it('redirects guest to login when accessing manage-grade', function () {
        $this->get('/manage-grade')->assertRedirect('/login');
    });

    it('redirects guest to login when accessing manage-settings', function () {
        $this->get('/manage-settings')->assertRedirect('/login');
    });
});

// ── Admin-only routes ────────────────────────────────────────────────────────

describe('Admin-only route protection', function () {
    it('allows admin to access manage-user', function () {
        loginAsAdmin();
        $this->get('/manage-user')->assertOk();
    });

    it('blocks murid from accessing manage-user', function () {
        loginAsMurid();
        $this->get('/manage-user')->assertRedirectToRoute('dashboard');
    });

    it('blocks guru from accessing manage-user', function () {
        loginAsGuru();
        $this->get('/manage-user')->assertRedirectToRoute('dashboard');
    });

    it('allows admin to access manage-class', function () {
        loginAsAdmin();
        $this->get('/manage-class')->assertOk();
    });

    it('blocks murid from accessing manage-class', function () {
        loginAsMurid();
        $this->get('/manage-class')->assertRedirectToRoute('dashboard');
    });

    it('blocks guru from accessing manage-class', function () {
        loginAsGuru();
        $this->get('/manage-class')->assertRedirectToRoute('dashboard');
    });

    it('allows admin to access manage-settings', function () {
        loginAsAdmin();
        $this->get('/manage-settings')->assertOk();
    });

    it('blocks guru from accessing manage-settings', function () {
        loginAsGuru();
        $this->get('/manage-settings')->assertRedirectToRoute('dashboard');
    });

    it('blocks murid from accessing manage-settings', function () {
        loginAsMurid();
        $this->get('/manage-settings')->assertRedirectToRoute('dashboard');
    });
});

// ── Guru-only routes ─────────────────────────────────────────────────────────

describe('Guru route access', function () {
    it('allows guru to access teacher-attendance', function () {
        loginAsGuru();
        $this->get('/teacher-attendance')->assertOk();
    });

    it('allows guru to access manage-grade', function () {
        loginAsGuru();
        $this->get('/manage-grade')->assertOk();
    });

    it('blocks murid from accessing manage-grade', function () {
        loginAsMurid();
        $this->get('/manage-grade')->assertRedirectToRoute('dashboard');
    });

    it('allows admin to access attendance-recap', function () {
        loginAsAdmin();
        $this->get('/attendance-recap')->assertOk();
    });

    it('blocks murid from accessing attendance-recap', function () {
        loginAsMurid();
        $this->get('/attendance-recap')->assertRedirectToRoute('dashboard');
    });
});

// ── Murid-only routes ────────────────────────────────────────────────────────

describe('Murid route access', function () {
    it('allows murid to access student-grade', function () {
        loginAsMurid();
        $this->get('/student-grade')->assertOk();
    });

    it('allows murid to access attendance', function () {
        loginAsMurid();
        $this->get('/attendance')->assertOk();
    });
});

// ── Authenticated (all roles) ────────────────────────────────────────────────

describe('Authenticated access to shared routes', function () {
    it('allows any authenticated user to access dashboard', function (string $role) {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user)->get('/dashboard')->assertOk();
    })->with('non_admin_roles');

    it('renders login page for guest', function () {
        $this->get('/login')->assertOk();
    });
});
