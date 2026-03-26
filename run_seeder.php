<?php

declare(strict_types=1);

use Database\Seeders\KelasSDSeeder;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

try {
    app(KelasSDSeeder::class)->run();
    file_put_contents('err.txt', 'SUCCESS');
} catch (Throwable $e) {
    file_put_contents('err.txt', $e->getMessage()."\n".$e->getFile().':'.$e->getLine());
}
