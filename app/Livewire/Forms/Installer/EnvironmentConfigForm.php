<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Installer;

use Livewire\Form;

class EnvironmentConfigForm extends Form
{
    // Application Settings
    public string $appEnv = 'production';

    public bool $appDebug = false;

    public string $appUrl = '';

    public string $appLocale = 'id';

    // Session Settings
    public string $sessionDriver = 'file';

    public int $sessionLifetime = 120;

    // Cache Settings
    public string $cacheStore = 'file';

    // Queue Settings
    public string $queueConnection = 'sync';

    // Mail Settings
    public string $mailMailer = 'log';

    public string $mailHost = '';

    public string $mailPort = '587';

    public string $mailUsername = '';

    public string $mailPassword = '';

    public string $mailEncryption = 'tls';

    public string $mailFromAddress = '';

    public string $mailFromName = '';

    // Turnstile Settings (Cloudflare)
    public string $turnstileSiteKey = '';

    public string $turnstileSecretKey = '';

    // Storage Settings
    public string $filesystemDisk = 'local';

    public string $mediaDisk = 'public';

    // AWS S3 Settings (when using s3)
    public string $awsAccessKeyId = '';

    public string $awsSecretAccessKey = '';

    public string $awsDefaultRegion = 'ap-southeast-1';

    public string $awsBucket = '';

    public string $awsUrl = '';

    public string $awsEndpoint = '';

    public bool $awsUsePathStyleEndpoint = false;

    protected function rules(): array
    {
        return [
            'appEnv' => 'required|in:local,production,staging',
            'appDebug' => 'boolean',
            'appUrl' => 'required|url',
            'appLocale' => 'required|in:id,en',
            'sessionDriver' => 'required|in:file,database,cookie,redis',
            'sessionLifetime' => 'required|integer|min:1|max:43200',
            'cacheStore' => 'required|in:file,database,redis,array',
            'queueConnection' => 'required|in:sync,database,redis',
            'mailMailer' => 'required|in:log,smtp,mailgun,ses,postmark',
            'mailHost' => 'nullable|string|max:255',
            'mailPort' => 'nullable|string|max:10',
            'mailUsername' => 'nullable|string|max:255',
            'mailPassword' => 'nullable|string|max:255',
            'mailEncryption' => 'nullable|in:tls,ssl,null',
            'mailFromAddress' => 'nullable|email|max:255',
            'mailFromName' => 'nullable|string|max:255',
            // Turnstile
            'turnstileSiteKey' => 'nullable|string|max:255',
            'turnstileSecretKey' => 'nullable|string|max:255',
            // Storage
            'filesystemDisk' => 'required|in:local,s3',
            'mediaDisk' => 'required|in:public,s3',
            // AWS S3 (required when filesystem or media is s3)
            'awsAccessKeyId' => 'nullable|required_if:filesystemDisk,s3|required_if:mediaDisk,s3|string|max:255',
            'awsSecretAccessKey' => 'nullable|required_if:filesystemDisk,s3|required_if:mediaDisk,s3|string|max:255',
            'awsDefaultRegion' => 'nullable|string|max:50',
            'awsBucket' => 'nullable|required_if:filesystemDisk,s3|required_if:mediaDisk,s3|string|max:255',
            'awsUrl' => 'nullable|url|max:255',
            'awsEndpoint' => 'nullable|url|max:255',
            'awsUsePathStyleEndpoint' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'appUrl.required' => 'Application URL is required',
            'appUrl.url' => 'Please enter a valid URL (e.g., https://example.com)',
            'sessionLifetime.min' => 'Session lifetime must be at least 1 minute',
        ];
    }

    public function mount(): void
    {
        // Set default APP_URL from current request
        $this->appUrl = request()->getSchemeAndHttpHost();
        $this->mailFromName = 'LPPM ITSNU';
    }

    public function getEnvConfig(): array
    {
        $this->normalizeInputs();

        return [
            'APP_ENV' => $this->appEnv,
            'APP_DEBUG' => $this->appDebug ? 'true' : 'false',
            'APP_URL' => $this->appUrl,
            'APP_LOCALE' => $this->appLocale,
            'SESSION_DRIVER' => $this->sessionDriver,
            'SESSION_LIFETIME' => (string) $this->sessionLifetime,
            'CACHE_STORE' => $this->cacheStore,
            'QUEUE_CONNECTION' => $this->queueConnection,
            'MAIL_MAILER' => $this->mailMailer,
            'MAIL_HOST' => $this->mailHost,
            'MAIL_PORT' => $this->mailPort,
            'MAIL_USERNAME' => $this->mailUsername,
            'MAIL_PASSWORD' => $this->mailPassword,
            'MAIL_ENCRYPTION' => $this->mailEncryption === 'null' ? '' : $this->mailEncryption,
            'MAIL_FROM_ADDRESS' => $this->mailFromAddress,
            'MAIL_FROM_NAME' => $this->mailFromName,
            // Turnstile
            'TURNSTILE_SITE_KEY' => $this->turnstileSiteKey,
            'TURNSTILE_SECRET_KEY' => $this->turnstileSecretKey,
            // Storage
            'FILESYSTEM_DISK' => $this->filesystemDisk,
            'MEDIA_DISK' => $this->mediaDisk,
            // AWS S3
            'AWS_ACCESS_KEY_ID' => $this->awsAccessKeyId,
            'AWS_SECRET_ACCESS_KEY' => $this->awsSecretAccessKey,
            'AWS_DEFAULT_REGION' => $this->awsDefaultRegion,
            'AWS_BUCKET' => $this->awsBucket,
            'AWS_URL' => $this->awsUrl,
            'AWS_ENDPOINT' => $this->awsEndpoint,
            'AWS_USE_PATH_STYLE_ENDPOINT' => $this->awsUsePathStyleEndpoint ? 'true' : 'false',
        ];
    }

    public function normalizeInputs(): void
    {
        $this->appUrl = rtrim(trim($this->appUrl), '/');
        $this->mailHost = trim($this->mailHost);
        $this->mailUsername = trim($this->mailUsername);
        $this->mailFromAddress = trim($this->mailFromAddress);
        $this->mailFromName = trim($this->mailFromName);
        $this->turnstileSiteKey = trim($this->turnstileSiteKey);
        $this->turnstileSecretKey = trim($this->turnstileSecretKey);
        $this->awsAccessKeyId = trim($this->awsAccessKeyId);
        $this->awsSecretAccessKey = trim($this->awsSecretAccessKey);
        $this->awsBucket = trim($this->awsBucket);
        $this->awsUrl = trim($this->awsUrl);
        $this->awsEndpoint = trim($this->awsEndpoint);
    }

    /**
     * Get available options for select fields.
     */
    public static function getOptions(): array
    {
        return [
            'appEnv' => [
                'production' => 'Production',
                'staging' => 'Staging',
                'local' => 'Local/Development',
            ],
            'appLocale' => [
                'id' => 'Indonesia',
                'en' => 'English',
            ],
            'sessionDriver' => [
                'file' => 'File (Recommended for single server)',
                'database' => 'Database',
                'cookie' => 'Cookie',
                'redis' => 'Redis (Requires Redis server)',
            ],
            'cacheStore' => [
                'file' => 'File (Recommended for single server)',
                'database' => 'Database',
                'redis' => 'Redis (Requires Redis server)',
                'array' => 'Array (No caching)',
            ],
            'queueConnection' => [
                'sync' => 'Sync (Run immediately)',
                'database' => 'Database (Background processing)',
                'redis' => 'Redis (Requires Redis server)',
            ],
            'mailMailer' => [
                'log' => 'Log (For testing only)',
                'smtp' => 'SMTP',
                'mailgun' => 'Mailgun',
                'ses' => 'Amazon SES',
                'postmark' => 'Postmark',
            ],
            'mailEncryption' => [
                'tls' => 'TLS',
                'ssl' => 'SSL',
                'null' => 'None',
            ],
            'filesystemDisk' => [
                'local' => 'Local (Default)',
                's3' => 'Amazon S3 / S3-Compatible',
            ],
            'mediaDisk' => [
                'public' => 'Public (Local storage)',
                's3' => 'Amazon S3 / S3-Compatible',
            ],
            'awsRegion' => [
                'ap-southeast-1' => 'Asia Pacific (Singapore)',
                'ap-southeast-2' => 'Asia Pacific (Sydney)',
                'ap-northeast-1' => 'Asia Pacific (Tokyo)',
                'ap-south-1' => 'Asia Pacific (Mumbai)',
                'us-east-1' => 'US East (N. Virginia)',
                'us-west-2' => 'US West (Oregon)',
                'eu-west-1' => 'Europe (Ireland)',
                'eu-central-1' => 'Europe (Frankfurt)',
            ],
        ];
    }
}
