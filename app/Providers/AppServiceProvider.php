<?php

namespace App\Providers;

use Illuminate\Database\Events\ConnectionEstablished;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $registerSqliteFunctions = function ($connection) {
            if ($connection instanceof SQLiteConnection) {
                $pdo = $connection->getPdo();
                if (method_exists($pdo, 'sqliteCreateFunction')) {
                    $pdo->sqliteCreateFunction('acos', function ($val) {
                        $clamped = max(-1.0, min(1.0, (float) $val));

                        return acos($clamped);
                    }, 1);
                    $pdo->sqliteCreateFunction('cos', fn ($val) => cos((float) $val), 1);
                    $pdo->sqliteCreateFunction('sin', fn ($val) => sin((float) $val), 1);
                    $pdo->sqliteCreateFunction('radians', fn ($val) => deg2rad((float) $val), 1);
                }
            }
        };

        // Attach to connection event for any newly created connections (especially in tests)
        Event::listen(ConnectionEstablished::class, function (ConnectionEstablished $event) use ($registerSqliteFunctions) {
            $registerSqliteFunctions($event->connection);
        });

        // Also register on current connection if already established
        if (DB::getDriverName() === 'sqlite') {
            $registerSqliteFunctions(DB::connection());
        }
    }
}
