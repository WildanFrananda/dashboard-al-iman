<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->withoutTwoFactor()->create();

    $response = $this->withoutMiddleware(ValidateCsrfToken::class)
        ->post(route('login.store'), [
            'email'    => $user->email,
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->withoutMiddleware(ValidateCsrfToken::class)
        ->post(route('login.store'), [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

    $response->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withoutMiddleware(ValidateCsrfToken::class)
        ->post(route('logout'));

    $response->assertRedirect(route('home'));
    $this->assertGuest();
});
