<?php

declare(strict_types=1);

namespace App\Services\Installer;

use Exception;
use Illuminate\Support\Facades\DB;

class DatabaseTester
{
    public function testConnection(array $config): array
    {
        try {
            // Create a temporary connection to test
            $tempConfig = config('database');
            $tempConfig['connections']['installer_test'] = [
                'driver' => $config['driver'] ?? 'mariadb',
                'host' => $config['host'],
                'port' => $config['port'] ?? 3306,
                'database' => $config['database'],
                'username' => $config['username'],
                'password' => $config['password'] ?? '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ];

            config(['database' => $tempConfig]);

            // Test connection
            $connection = DB::connection('installer_test');
            $connection->getPdo();

            return [
                'success' => true,
                'message' => 'Connection successful',
                'details' => [
                    'driver' => $connection->getDriverName(),
                    'server_version' => $connection->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION),
                ],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $this->parseErrorMessage($e->getMessage()),
                'details' => null,
            ];
        }
    }

    public function testCredentialsOnly(array $config): array
    {
        try {
            // Test connection without selecting database
            $dsn = sprintf(
                '%s:host=%s;port=%s',
                $config['driver'] ?? 'mysql',
                $config['host'],
                $config['port'] ?? 3306
            );

            $pdo = new \PDO(
                $dsn,
                $config['username'],
                $config['password'] ?? '',
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_TIMEOUT => 5,
                ]
            );

            return [
                'success' => true,
                'message' => 'Credentials valid',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $this->parseErrorMessage($e->getMessage()),
            ];
        }
    }

    public function databaseExists(array $config): bool
    {
        try {
            $dsn = sprintf(
                '%s:host=%s;port=%s',
                $config['driver'] ?? 'mysql',
                $config['host'],
                $config['port'] ?? 3306
            );

            $pdo = new \PDO(
                $dsn,
                $config['username'],
                $config['password'] ?? '',
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                ]
            );

            $stmt = $pdo->prepare('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?');
            $stmt->execute([$config['database']]);

            return $stmt->fetch() !== false;
        } catch (Exception) {
            return false;
        }
    }

    public function createDatabase(array $config): array
    {
        try {
            $dsn = sprintf(
                '%s:host=%s;port=%s',
                $config['driver'] ?? 'mysql',
                $config['host'],
                $config['port'] ?? 3306
            );

            $pdo = new \PDO(
                $dsn,
                $config['username'],
                $config['password'] ?? '',
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                ]
            );

            $database = $config['database'];
            $charset = 'utf8mb4';
            $collation = 'utf8mb4_unicode_ci';

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET {$charset} COLLATE {$collation}");

            return [
                'success' => true,
                'message' => "Database '{$database}' created successfully",
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $this->parseErrorMessage($e->getMessage()),
            ];
        }
    }

    private function parseErrorMessage(string $message): string
    {
        // Make error messages more user-friendly
        if (str_contains($message, 'Access denied')) {
            return 'Access denied: Invalid username or password';
        }

        if (str_contains($message, 'Connection refused')) {
            return 'Connection refused: Cannot connect to database server. Check host and port.';
        }

        if (str_contains($message, 'Connection timed out')) {
            return 'Connection timed out: Database server is not responding';
        }

        if (str_contains($message, 'Unknown database')) {
            return 'Database does not exist';
        }

        if (str_contains($message, 'getaddrinfo failed')) {
            return 'Cannot resolve database host. Check the hostname.';
        }

        return $message;
    }

    public function getDefaultValues(): array
    {
        $currentEnv = (new EnvironmentWriter)->readCurrent();

        return [
            'driver' => $currentEnv['DB_CONNECTION'] ?? 'mariadb',
            'host' => $currentEnv['DB_HOST'] ?? '127.0.0.1',
            'port' => $currentEnv['DB_PORT'] ?? '3306',
            'database' => $currentEnv['DB_DATABASE'] ?? 'lppm_itsnu',
            'username' => $currentEnv['DB_USERNAME'] ?? 'root',
            'password' => $currentEnv['DB_PASSWORD'] ?? '',
        ];
    }
}
