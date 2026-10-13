<?php

/**
 * ONE-TIME SUPER ADMIN & SEED RUNNER FOR HOSTINGER SHARED HOSTING
 * ----------------------------------------------------------------
 * Access: https://yourdomain.com/seed-run.php?token=PMS_HOSTINGER_DEPLOY_2026
 *
 * IMPORTANT: Delete this file from public/ immediately after use!
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
echo "=== PartyManager (PMS) - Super Admin & Seeder Runner ===\n\n";

// 1. Ensure Super Admin Account
try {
    $admin = \App\Models\User::updateOrCreate(
        ['email' => 'infiniteatpc7@gmail.com'],
        [
            'name' => 'Super Admin',
            'password' => \Illuminate\Support\Facades\Hash::make('Admin@123#'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]
    );

    echo "✓ Super Admin User Configured Successfully!\n";
    echo "  Email:    infiniteatpc7@gmail.com\n";
    echo "  Role:     admin\n";
    echo "  Password: Admin@123#\n\n";
} catch (\Throwable $e) {
    echo "✗ Super Admin setup error: " . $e->getMessage() . "\n\n";
}

// 2. Run Database Seeders (Parties)
try {
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    echo \Illuminate\Support\Facades\Artisan::output();
    echo "✓ Seeders executed successfully!\n\n";
} catch (\Throwable $e) {
    echo "Notice on seeders: " . $e->getMessage() . "\n\n";
}

// 3. Re-cache Optimization Assets
try {
    \Illuminate\Support\Facades\Artisan::call('config:cache');
    \Illuminate\Support\Facades\Artisan::call('route:cache');
    \Illuminate\Support\Facades\Artisan::call('view:cache');
    echo "✓ Config, routes, and views cached.\n\n";
} catch (\Throwable $e) {
    echo "Notice on cache: " . $e->getMessage() . "\n\n";
}

echo "========================================================\n";
echo "IMPORTANT: Delete seed-run.php from public/ NOW!\n";
echo "========================================================\n";
echo '</pre>';
