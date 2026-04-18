<?php

declare(strict_types=1);

use App\Livewire\ManageUser;
use App\Models\User;

beforeEach(function () {
    loginAsAdmin();
});

describe('ManageUser — listing', function () {
    it('shows user list on mount', function () {
        $user = User::factory()->create(['name' => 'Budi Santoso']);

        Livewire\Livewire::test(ManageUser::class)
            ->assertSee('Budi Santoso');
    });

    it('filters users by search term', function () {
        User::factory()->create(['name' => 'Rina Guru']);
        User::factory()->create(['name' => 'Andi Murid']);

        Livewire\Livewire::test(ManageUser::class)
            ->set('search', 'Rina')
            ->assertSee('Rina Guru')
            ->assertDontSee('Andi Murid');
    });

    it('filters users by role', function () {
        User::factory()->guru()->create(['name' => 'Guru Satu']);
        User::factory()->murid()->create(['name' => 'Murid Satu']);

        Livewire\Livewire::test(ManageUser::class)
            ->set('selectedRole', 'guru')
            ->assertSee('Guru Satu')
            ->assertDontSee('Murid Satu');
    });
});

describe('ManageUser — modal state', function () {
    it('opens add modal and resets form on openAddForm', function () {
        Livewire\Livewire::test(ManageUser::class)
            ->call('openAddForm')
            ->assertSet('showModal', true)
            ->assertSet('editingId', null)
            ->assertSet('form_name', '');
    });

    it('prefills form with user data on openEditForm', function () {
        $user = User::factory()->guru()->create(['name' => 'Edit Target']);

        Livewire\Livewire::test(ManageUser::class)
            ->call('openEditForm', $user->id)
            ->assertSet('showModal', true)
            ->assertSet('editingId', $user->id)
            ->assertSet('form_name', 'Edit Target');
    });

    it('closes modal and resets form on closeModal', function () {
        Livewire\Livewire::test(ManageUser::class)
            ->call('openAddForm')
            ->call('closeModal')
            ->assertSet('showModal', false)
            ->assertSet('form_name', '');
    });
});

describe('ManageUser — CRUD', function () {
    it('creates new user with valid data', function () {
        Livewire\Livewire::test(ManageUser::class)
            ->set('form_name', 'New User')
            ->set('form_email', 'newuser@example.com')
            ->set('form_role', 'guru')
            ->set('form_password', 'password123')
            ->call('saveUser');

        expect(User::where('email', 'newuser@example.com')->exists())->toBeTrue();
    });

    it('validates name is required', function () {
        Livewire\Livewire::test(ManageUser::class)
            ->set('form_name', '')
            ->set('form_email', 'test@test.com')
            ->set('form_role', 'murid')
            ->set('form_password', 'password123')
            ->call('saveUser')
            ->assertHasErrors(['form_name']);
    });

    it('validates email must be unique', function () {
        User::factory()->create(['email' => 'taken@example.com']);

        Livewire\Livewire::test(ManageUser::class)
            ->set('form_name', 'Another User')
            ->set('form_email', 'taken@example.com')
            ->set('form_role', 'murid')
            ->set('form_password', 'password123')
            ->call('saveUser')
            ->assertHasErrors(['form_email']);
    });

    it('validates password minimum 8 characters on create', function () {
        Livewire\Livewire::test(ManageUser::class)
            ->set('form_name', 'Short Pass')
            ->set('form_email', 'short@example.com')
            ->set('form_role', 'murid')
            ->set('form_password', '123')
            ->call('saveUser')
            ->assertHasErrors(['form_password']);
    });

    it('updates user without changing password when password left empty', function () {
        $user = User::factory()->create(['email' => 'edit@example.com']);
        $originalHash = $user->password;

        Livewire\Livewire::test(ManageUser::class)
            ->call('openEditForm', $user->id)
            ->set('form_name', 'Updated Name')
            ->set('form_password', '')
            ->call('saveUser');

        expect($user->fresh()->name)->toBe('Updated Name');
        expect($user->fresh()->password)->toBe($originalHash);
    });

    it('deletes user', function () {
        $user = User::factory()->create();

        Livewire\Livewire::test(ManageUser::class)
            ->call('deleteUser', $user->id);

        expect(User::find($user->id))->toBeNull();
    });

    it('prevents admin from deleting their own account', function () {
        $admin = User::where('role', 'admin')->first();

        Livewire\Livewire::test(ManageUser::class)
            ->call('deleteUser', $admin->id);

        expect(User::find($admin->id))->not->toBeNull();
    });
});
