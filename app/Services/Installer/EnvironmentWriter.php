<?php

declare(strict_types=1);

namespace App\Services\Installer;

use Exception;
use Illuminate\Support\Facades\File;

class EnvironmentWriter
{
    public function write(array $config): void
    {
        $envPath = base_path('.env');
        $examplePath = base_path('.env.example');

        if (! File::exists($examplePath)) {
            throw new Exception('.env.example file not found');
        }

        $content = File::get($examplePath);

        // Update database configuration
        $content = $this->updateValue($content, 'DB_CONNECTION', $config['db_connection'] ?? 'mariadb');
        $content = $this->updateValue($content, 'DB_HOST', $config['db_host'] ?? '127.0.0.1');
        $content = $this->updateValue($content, 'DB_PORT', $config['db_port'] ?? '3306');
        $content = $this->updateValue($content, 'DB_DATABASE', $config['db_database'] ?? 'laravel');
        $content = $this->updateValue($content, 'DB_USERNAME', $config['db_username'] ?? 'root');
        $content = $this->updateValue($content, 'DB_PASSWORD', $config['db_password'] ?? '');

        // Update application configuration
        $content = $this->updateValue($content, 'APP_NAME', $config['app_name'] ?? 'LPPM-ITSNU');
        $content = $this->updateValue($content, 'APP_ENV', $config['app_env'] ?? 'production');
        $content = $this->updateValue($content, 'APP_URL', $config['app_url'] ?? 'http://localhost');
        $content = $this->updateValue($content, 'APP_LOCALE', $config['app_locale'] ?? 'id');

        // Update mail configuration
        if (! empty($config['mail_mailer'])) {
            $content = $this->updateValue($content, 'MAIL_MAILER', $config['mail_mailer']);
        }
        if (! empty($config['mail_host'])) {
            $content = $this->updateValue($content, 'MAIL_HOST', $config['mail_host']);
        }
        if (! empty($config['mail_port'])) {
            $content = $this->updateValue($content, 'MAIL_PORT', $config['mail_port']);
        }
        if (! empty($config['mail_from_address'])) {
            $content = $this->updateValue($content, 'MAIL_FROM_ADDRESS', $config['mail_from_address']);
        }

        // Update Turnstile configuration
        if (! empty($config['turnstile_site_key'])) {
            $content = $this->updateValue($content, 'TURNSTILE_SITE_KEY', $config['turnstile_site_key']);
        }
        if (! empty($config['turnstile_secret_key'])) {
            $content = $this->updateValue($content, 'TURNSTILE_SECRET_KEY', $config['turnstile_secret_key']);
        }

        File::put($envPath, $content);

        // Reload configuration
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    private function updateValue(string $content, string $key, string $value): string
    {
        // Escape special characters in value
        $value = $this->escapeValue($value);

        // Pattern to match the key at the start of a line
        $pattern = '/^'.preg_quote($key).'=.*/m';

        if (preg_match($pattern, $content)) {
            // Update existing key
            $content = preg_replace($pattern, $key.'='.$value, $content);
        } else {
            // Add new key at the end
            $content .= "\n{$key}={$value}";
        }

        return $content;
    }

    private function escapeValue(string $value): string
    {
        // If value contains spaces or special characters, wrap in quotes
        if (str_contains($value, ' ') || str_contains($value, '#') || str_contains($value, '"')) {
            $value = '"'.str_replace('"', '\\"', $value).'"';
        }

        return $value;
    }

    public function readCurrent(): array
    {
        $envPath = base_path('.env');

        if (! File::exists($envPath)) {
            return [];
        }

        $content = File::get($envPath);
        $values = [];

        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $line = trim($line);

            // Skip comments and empty lines
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            // Parse key=value
            if (str_contains($line, '=')) {
                $parts = explode('=', $line, 2);
                $key = trim($parts[0]);
                $value = trim($parts[1] ?? '');

                // Remove quotes
                if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
                    $value = substr($value, 1, -1);
                    $value = str_replace('\\"', '"', $value);
                }

                $values[$key] = $value;
            }
        }

        return $values;
    }
}
