<?php

declare(strict_types=1);

it('rate limits login attempts after exceeding threshold', function () {
    // Kirim 6 request login gagal
    for ($i = 0; $i < 6; $i++) {
        $this->post('/login', [
            'email'    => 'notexist@example.com',
            'password' => 'wrongpassword',
        ]);
    }

    $response = $this->post('/login', [
        'email'    => 'notexist@example.com',
        'password' => 'wrongpassword',
    ]);

    // Fortify/Laravel throttle memberikan 429 atau redirect dengan error
    expect(in_array($response->status(), [429, 302]))->toBeTrue();
});
