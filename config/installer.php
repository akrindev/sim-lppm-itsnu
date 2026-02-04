<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Installer Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether the web installer is enabled. When set
    | to false, the installer middleware will not redirect uninstalled
    | applications to the installer route.
    |
    */
    'enabled' => env('INSTALLER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Auto Initialize
    |--------------------------------------------------------------------------
    |
    | When enabled, the application will attempt to auto-install on first
    | boot using environment variables. This is useful for containerized
    | deployments.
    |
    */
    'auto_init' => env('INSTALLER_AUTO_INIT', false),

    /*
    |--------------------------------------------------------------------------
    | Lock File
    |--------------------------------------------------------------------------
    |
    | The path to the lock file that indicates installation is complete.
    | This is relative to the storage path.
    |
    */
    'lock_file' => env('INSTALLER_LOCK_FILE', 'app/.installed'),

    /*
    |--------------------------------------------------------------------------
    | Environment Checks
    |--------------------------------------------------------------------------
    |
    | Configure which environment checks should be performed during
    | the installation process.
    |
    */
    'checks' => [
        'php_version' => '8.2.0',
        'extensions' => [
            'pdo',
            'pdo_mysql',
            'mbstring',
            'openssl',
            'tokenizer',
            'json',
            'ctype',
            'xml',
            'bcmath',
            'fileinfo',
        ],
        'writable_directories' => [
            'storage',
            'storage/app',
            'storage/framework',
            'storage/logs',
            'bootstrap/cache',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Database
    |--------------------------------------------------------------------------
    |
    | Default database configuration values for the installer.
    |
    */
    'database' => [
        'driver' => 'mariadb',
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'lppm_itsnu',
        'username' => 'root',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Institution
    |--------------------------------------------------------------------------
    |
    | Default institution configuration for the installer.
    |
    */
    'institution' => [
        'name' => 'Institut Teknologi dan Sains Nahdlatul Ulama Pekalongan',
        'short_name' => 'ITSNU Pekalongan',
        'address' => '',
        'phone' => '',
        'email' => 'info@itsnu.ac.id',
        'website' => 'https://itsnu.ac.id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Faculties
    |--------------------------------------------------------------------------
    |
    | Default faculties to create during installation.
    |
    */
    'faculties' => [
        ['name' => 'Fakultas Sains dan Teknologi', 'code' => 'SAINTEK'],
        ['name' => 'Fakultas Desain Kreatif dan Bisnis Digital', 'code' => 'DEKABITA'],
    ],
];
