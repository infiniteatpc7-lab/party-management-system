<?php

/**
 * ONE-TIME MIGRATION RUNNER FOR HOSTINGER SHARED HOSTING
 * -------------------------------------------------------
 * Use this in your browser if you cannot run SSH terminal commands on Hostinger.
 * Access: https://yourdomain.com/migrate-run.php?token=PMS_HOSTINGER_DEPLOY_2026
 *
 * IMPORTANT: Delete this file from public/ immediately after running!
 */

$secret = 'PMS_HOSTINGER_DEPLOY_2026';

if (!isset($_GET['token']) || $_GET['token'] !== $secret) {
    http_response_code(403);
    die('Forbidden. Provide valid ?token= parameter.');
}

define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo '<pre style="font-family:monospace;background:#141414;color:#E4FD97;padding:24px;border-radius:12px;line-height:1.6;font-size:13px;">';
echo "========================================================\n";
echo "  PartyManager (PMS) - Hostinger Migration & Setup Runner\n";
echo "========================================================\n\n";

// 1. Run Database Migrations
try {
    echo "[1/4] Running Database Migrations...\n";
    $exitCode = \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo \Illuminate\Support\Facades\Artisan::output();
    echo "✓ Migrations completed successfully!\n\n";
} catch (\Throwable $e) {
    echo "✗ Migration error: " . $e->getMessage() . "\n\n";
}

// 2. Run Database Seeders (creates Super Admin: infiniteatpc7@gmail.com & sample venues)
try {
    echo "[2/4] Seeding Database (Super Admin & Data)...\n";
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    echo \Illuminate\Support\Facades\Artisan::output();
    echo "✓ Database seeded successfully!\n";
    echo "  Super Admin: infiniteatpc7@gmail.com\n\n";
} catch (\Throwable $e) {
    echo "✗ Seeding error: " . $e->getMessage() . "\n\n";
}

// 3. Storage Link
try {
    echo "[3/4] Creating Storage Symlink...\n";
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    echo \Illuminate\Support\Facades\Artisan::output();
    echo "✓ Storage symlink ready!\n\n";
} catch (\Throwable $e) {
    echo "Notice on storage link: " . $e->getMessage() . "\n\n";
}

// 4. Cache Config, Routes & Views for Maximum Performance
try {
    echo "[4/4] Caching Optimization Assets...\n";
    \Illuminate\Support\Facades\Artisan::call('config:cache');
    echo "✓ Config cached.\n";
    \Illuminate\Support\Facades\Artisan::call('route:cache');
    echo "✓ Routes cached.\n";
    \Illuminate\Support\Facades\Artisan::call('view:cache');
    echo "✓ Views cached.\n\n";
} catch (\Throwable $e) {
    echo "Notice on caching: " . $e->getMessage() . "\n\n";
}

echo "========================================================\n";
echo "SETUP COMPLETED SUCCESSFULLY!\n";
echo "CRITICAL: Delete migrate-run.php from public/ NOW for security.\n";
echo "========================================================\n";
echo '</pre>';
