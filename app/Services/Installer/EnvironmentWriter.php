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
        $content = $this->updateValue($content, 'DB_CONNECTION', $this->resolveValue($config, ['DB_CONNECTION', 'db_connection'], 'mariadb'));
        $content = $this->updateValue($content, 'DB_HOST', $this->resolveValue($config, ['DB_HOST', 'db_host'], '127.0.0.1'));
        $content = $this->updateValue($content, 'DB_PORT', $this->resolveValue($config, ['DB_PORT', 'db_port'], '3306'));
        $content = $this->updateValue($content, 'DB_DATABASE', $this->resolveValue($config, ['DB_DATABASE', 'db_database'], 'laravel'));
        $content = $this->updateValue($content, 'DB_USERNAME', $this->resolveValue($config, ['DB_USERNAME', 'db_username'], 'root'));
        $content = $this->updateValue($content, 'DB_PASSWORD', $this->resolveValue($config, ['DB_PASSWORD', 'db_password'], ''));

        // Update application configuration
        $content = $this->updateValue($content, 'APP_NAME', $this->resolveValue($config, ['APP_NAME', 'app_name'], 'LPPM-ITSNU'));
        $content = $this->updateValue($content, 'APP_ENV', $this->resolveValue($config, ['APP_ENV', 'app_env'], 'production'));
        if ($this->hasConfigKey($config, ['APP_DEBUG', 'app_debug'])) {
            $content = $this->updateValue($content, 'APP_DEBUG', $this->resolveValue($config, ['APP_DEBUG', 'app_debug'], 'false'));
        }
        $content = $this->updateValue($content, 'APP_URL', $this->resolveValue($config, ['APP_URL', 'app_url'], 'http://localhost'));
        $content = $this->updateValue($content, 'APP_LOCALE', $this->resolveValue($config, ['APP_LOCALE', 'app_locale'], 'id'));

        // Update session/cache/queue configuration
        if ($this->hasConfigKey($config, ['SESSION_DRIVER', 'session_driver'])) {
            $content = $this->updateValue($content, 'SESSION_DRIVER', $this->resolveValue($config, ['SESSION_DRIVER', 'session_driver'], 'file'));
        }
        if ($this->hasConfigKey($config, ['SESSION_LIFETIME', 'session_lifetime'])) {
            $content = $this->updateValue($content, 'SESSION_LIFETIME', $this->resolveValue($config, ['SESSION_LIFETIME', 'session_lifetime'], '120'));
        }
        if ($this->hasConfigKey($config, ['CACHE_STORE', 'cache_store'])) {
            $content = $this->updateValue($content, 'CACHE_STORE', $this->resolveValue($config, ['CACHE_STORE', 'cache_store'], 'file'));
        }
        if ($this->hasConfigKey($config, ['QUEUE_CONNECTION', 'queue_connection'])) {
            $content = $this->updateValue($content, 'QUEUE_CONNECTION', $this->resolveValue($config, ['QUEUE_CONNECTION', 'queue_connection'], 'sync'));
        }

        // Update mail configuration
        if ($this->hasConfigKey($config, ['MAIL_MAILER', 'mail_mailer'])) {
            $content = $this->updateValue($content, 'MAIL_MAILER', $this->resolveValue($config, ['MAIL_MAILER', 'mail_mailer'], 'log'));
        }
        if ($this->hasConfigKey($config, ['MAIL_HOST', 'mail_host'])) {
            $content = $this->updateValue($content, 'MAIL_HOST', $this->resolveValue($config, ['MAIL_HOST', 'mail_host'], ''));
        }
        if ($this->hasConfigKey($config, ['MAIL_PORT', 'mail_port'])) {
            $content = $this->updateValue($content, 'MAIL_PORT', $this->resolveValue($config, ['MAIL_PORT', 'mail_port'], ''));
        }
        if ($this->hasConfigKey($config, ['MAIL_USERNAME', 'mail_username'])) {
            $content = $this->updateValue($content, 'MAIL_USERNAME', $this->resolveValue($config, ['MAIL_USERNAME', 'mail_username'], ''));
        }
        if ($this->hasConfigKey($config, ['MAIL_PASSWORD', 'mail_password'])) {
            $content = $this->updateValue($content, 'MAIL_PASSWORD', $this->resolveValue($config, ['MAIL_PASSWORD', 'mail_password'], ''));
        }
        if ($this->hasConfigKey($config, ['MAIL_ENCRYPTION', 'mail_encryption'])) {
            $content = $this->updateValue($content, 'MAIL_ENCRYPTION', $this->resolveValue($config, ['MAIL_ENCRYPTION', 'mail_encryption'], ''));
        }
        if ($this->hasConfigKey($config, ['MAIL_FROM_ADDRESS', 'mail_from_address'])) {
            $content = $this->updateValue($content, 'MAIL_FROM_ADDRESS', $this->resolveValue($config, ['MAIL_FROM_ADDRESS', 'mail_from_address'], ''));
        }
        if ($this->hasConfigKey($config, ['MAIL_FROM_NAME', 'mail_from_name'])) {
            $content = $this->updateValue($content, 'MAIL_FROM_NAME', $this->resolveValue($config, ['MAIL_FROM_NAME', 'mail_from_name'], ''));
        }

        // Update Turnstile configuration
        if ($this->hasConfigKey($config, ['TURNSTILE_SITE_KEY', 'turnstile_site_key'])) {
            $content = $this->updateValue($content, 'TURNSTILE_SITE_KEY', $this->resolveValue($config, ['TURNSTILE_SITE_KEY', 'turnstile_site_key'], ''));
        }
        if ($this->hasConfigKey($config, ['TURNSTILE_SECRET_KEY', 'turnstile_secret_key'])) {
            $content = $this->updateValue($content, 'TURNSTILE_SECRET_KEY', $this->resolveValue($config, ['TURNSTILE_SECRET_KEY', 'turnstile_secret_key'], ''));
        }

        // Update filesystem configuration
        if ($this->hasConfigKey($config, ['FILESYSTEM_DISK', 'filesystem_disk'])) {
            $content = $this->updateValue($content, 'FILESYSTEM_DISK', $this->resolveValue($config, ['FILESYSTEM_DISK', 'filesystem_disk'], 'local'));
        }
        if ($this->hasConfigKey($config, ['MEDIA_DISK', 'media_disk'])) {
            $content = $this->updateValue($content, 'MEDIA_DISK', $this->resolveValue($config, ['MEDIA_DISK', 'media_disk'], 'public'));
        }

        // Update AWS S3 configuration
        if ($this->hasConfigKey($config, ['AWS_ACCESS_KEY_ID', 'aws_access_key_id'])) {
            $content = $this->updateValue($content, 'AWS_ACCESS_KEY_ID', $this->resolveValue($config, ['AWS_ACCESS_KEY_ID', 'aws_access_key_id'], ''));
        }
        if ($this->hasConfigKey($config, ['AWS_SECRET_ACCESS_KEY', 'aws_secret_access_key'])) {
            $content = $this->updateValue($content, 'AWS_SECRET_ACCESS_KEY', $this->resolveValue($config, ['AWS_SECRET_ACCESS_KEY', 'aws_secret_access_key'], ''));
        }
        if ($this->hasConfigKey($config, ['AWS_DEFAULT_REGION', 'aws_default_region'])) {
            $content = $this->updateValue($content, 'AWS_DEFAULT_REGION', $this->resolveValue($config, ['AWS_DEFAULT_REGION', 'aws_default_region'], 'ap-southeast-1'));
        }
        if ($this->hasConfigKey($config, ['AWS_BUCKET', 'aws_bucket'])) {
            $content = $this->updateValue($content, 'AWS_BUCKET', $this->resolveValue($config, ['AWS_BUCKET', 'aws_bucket'], ''));
        }
        if ($this->hasConfigKey($config, ['AWS_URL', 'aws_url'])) {
            $content = $this->updateValue($content, 'AWS_URL', $this->resolveValue($config, ['AWS_URL', 'aws_url'], ''));
        }
        if ($this->hasConfigKey($config, ['AWS_ENDPOINT', 'aws_endpoint'])) {
            $content = $this->updateValue($content, 'AWS_ENDPOINT', $this->resolveValue($config, ['AWS_ENDPOINT', 'aws_endpoint'], ''));
        }
        if ($this->hasConfigKey($config, ['AWS_USE_PATH_STYLE_ENDPOINT', 'aws_use_path_style_endpoint'])) {
            $content = $this->updateValue($content, 'AWS_USE_PATH_STYLE_ENDPOINT', $this->resolveValue($config, ['AWS_USE_PATH_STYLE_ENDPOINT', 'aws_use_path_style_endpoint'], 'false'));
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

    private function resolveValue(array $config, array $keys, string $default): string
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $config)) {
                return $this->stringifyValue($config[$key]);
            }
        }

        return $default;
    }

    private function hasConfigKey(array $config, array $keys): bool
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $config)) {
                return true;
            }
        }

        return false;
    }

    private function stringifyValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) $value;
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
