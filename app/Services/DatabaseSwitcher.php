<?php

namespace App\Services;

use PDO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class DatabaseSwitcher
{
    private static ?PDO $connection = null;
    private static ?string $currentRole = null;

    public static function switchConnection(string $role): void
    {
        if (self::$currentRole === $role) {
            Log::info("Already connected as $role");
            return;
        }

        try {
            $credentials = match ($role) {
                'admin' => [
                    'username' => 'laravel_admin',
                    'password' => config('database.connections.pgsql.password')
                ],
                'staff' => [
                    'username' => 'laravel_staff',
                    'password' => config('database.connections.pgsql.password')
                ],
                'user' => [
                    'username' => 'laravel_user',
                    'password' => config('database.connections.pgsql.password')
                ],
                default => throw new \InvalidArgumentException("Invalid role: $role"),
            };

            // Update config without purging the connection
            Config::set('database.connections.pgsql.username', $credentials['username']);
            Config::set('database.connections.pgsql.password', $credentials['password']);

            // Reconnect with new credentials
            DB::reconnect('pgsql');
            self::$connection = DB::connection('pgsql')->getPdo();
            self::$currentRole = $role;

            // Verify connection
            $testQuery = self::$connection->query('SELECT current_user')->fetchColumn();
            Log::info("Database connection verified", [
                'current_user' => $testQuery,
                'role' => $role
            ]);

        } catch (\Exception $e) {
            Log::error("Database connection failed: " . $e->getMessage(), [
                'role' => $role,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    public static function getCurrentRole(): ?string
    {
        return self::$currentRole;
    }

    public static function getConnection(): ?PDO
    {
        return self::$connection;
    }
}
