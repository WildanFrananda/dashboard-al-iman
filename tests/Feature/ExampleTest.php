<?php

declare(strict_types=1);

test('returns a successful response', function () {
    $response = $this->get('/');

    // '/' redirects guests to login
    $response->assertRedirect('/login');
});
