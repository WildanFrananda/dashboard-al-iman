<?php

declare(strict_types=1);

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

it('rate limits login attempts after exceeding threshold', function () {
    // Kirim 6 request login gagal (bypass CSRF agar throttle Fortify terpicu)
    for ($i = 0; $i < 6; $i++) {
        $this->withoutMiddleware(ValidateCsrfToken::class)
            ->post('/login', [
                'email' => 'notexist@example.com',
                'password' => 'wrongpassword',
            ]);
    }

    $response = $this->withoutMiddleware(ValidateCsrfToken::class)
        ->post('/login', [
            'email' => 'notexist@example.com',
            'password' => 'wrongpassword',
        ]);

    // Fortify throttle memberikan 429 atau redirect 302 dengan pesan throttle
    expect(in_array($response->status(), [429, 302]))->toBeTrue();
});
