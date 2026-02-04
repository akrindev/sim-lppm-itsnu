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
            // Create a temporary connection to test with full MariaDB/MySQL compatibility
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
                'options' => [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_TIMEOUT => 5,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
                ],
            ];

            config(['database' => $tempConfig]);

            // Purge any existing connection to ensure fresh test
            DB::purge('installer_test');

            // Test connection
            $connection = DB::connection('installer_test');
            $pdo = $connection->getPdo();

            // Test actual query execution
            $result = $pdo->query('SELECT 1 as test')->fetch();

            if (! $result || $result['test'] !== 1) {
                throw new Exception('Database query test failed');
            }

            return [
                'success' => true,
                'message' => 'Koneksi database berhasil',
                'details' => [
                    'driver' => $connection->getDriverName(),
                    'server_version' => $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION),
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
            // Test connection without selecting database with full MariaDB compatibility
            $dsn = sprintf(
                '%s:host=%s;port=%s;charset=utf8mb4',
                $config['driver'] === 'mariadb' ? 'mysql' : ($config['driver'] ?? 'mysql'),
                $config['host'],
                $config['port'] ?? 3306
            );

            $pdo = new \PDO(
                $dsn,
                $config['username'],
                $config['password'] ?? '',
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_TIMEOUT => 5,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
                ]
            );

            // Test by getting server version
            $version = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);

            return [
                'success' => true,
                'message' => "Credentials valid (Server: {$version})",
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
                '%s:host=%s;port=%s;charset=utf8mb4',
                $config['driver'] === 'mariadb' ? 'mysql' : ($config['driver'] ?? 'mysql'),
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
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
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
                '%s:host=%s;port=%s;charset=utf8mb4',
                $config['driver'] === 'mariadb' ? 'mysql' : ($config['driver'] ?? 'mysql'),
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
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
                ]
            );

            $database = $config['database'];
            $charset = $config['charset'] ?? 'utf8mb4';
            $collation = $config['collation'] ?? 'utf8mb4_unicode_ci';

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
