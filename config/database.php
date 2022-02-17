<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],



        'logins' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('LOGINS_HOST', '127.0.0.1'),
            'port' => env('LOGINS_PORT', '3306'),
            'database' => env('LOGINS_DATABASE', 'forge'),
            'username' => env('LOGINS_USERNAME', 'forge'),
            'password' => env('LOGINS_PASSWORD', ''),
            'unix_socket' => env('LOGINS_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],


        'arya' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('ARYA_HOST', '127.0.0.1'),
            'port' => env('ARYA_PORT', '3306'),
            'database' => env('ARYA_DATABASE', 'forge'),
            'username' => env('ARYA_USERNAME', 'forge'),
            'password' => env('ARYA_PASSWORD', ''),
            'unix_socket' => env('ARYA_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        
        'gsa' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('GSA_HOST', '127.0.0.1'),
            'port' => env('GSA_PORT', '3306'),
            'database' => env('GSA_DATABASE', 'forge'),
            'username' => env('GSA_USERNAME', 'forge'),
            'password' => env('GSA_PASSWORD', ''),
            'unix_socket' => env('GSA_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'aryanorthdelivery' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('ARYANORTHDELIVERY_HOST', '127.0.0.1'),
            'port' => env('ARYANORTHDELIVERY_PORT', '3306'),
            'database' => env('ARYANORTHDELIVERY_DATABASE', 'forge'),
            'username' => env('ARYANORTHDELIVERY_USERNAME', 'forge'),
            'password' => env('ARYANORTHDELIVERY_PASSWORD', ''),
            'unix_socket' => env('ARYANORTHDELIVERY_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'donapaula' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DONAPAULA_HOST', '127.0.0.1'),
            'port' => env('DONAPAULA_PORT', '3306'),
            'database' => env('DONAPAULA_DATABASE', 'forge'),
            'username' => env('DONAPAULA_USERNAME', 'forge'),
            'password' => env('DONAPAULA_PASSWORD', ''),
            'unix_socket' => env('DONAPAULA_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'fabriquim' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('FABRIQUIM_HOST', '127.0.0.1'),
            'port' => env('FABRIQUIM_PORT', '3306'),
            'database' => env('FABRIQUIM_DATABASE', 'forge'),
            'username' => env('FABRIQUIM_USERNAME', 'forge'),
            'password' => env('FABRIQUIM_PASSWORD', ''),
            'unix_socket' => env('FABRIQUIM_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'aryapopcorp' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('ARYAPOPCORP_HOST', '127.0.0.1'),
            'port' => env('ARYAPOPCORP_PORT', '3306'),
            'database' => env('ARYAPOPCORP_DATABASE', 'forge'),
            'username' => env('ARYAPOPCORP_USERNAME', 'forge'),
            'password' => env('ARYAPOPCORP_PASSWORD', ''),
            'unix_socket' => env('ARYAPOPCORP_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'gianestballoon' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('GIANESTBALLOON_HOST', '127.0.0.1'),
            'port' => env('GIANESTBALLOON_PORT', '3306'),
            'database' => env('GIANESTBALLOON_DATABASE', 'forge'),
            'username' => env('GIANESTBALLOON_USERNAME', 'forge'),
            'password' => env('GIANESTBALLOON_PASSWORD', ''),
            'unix_socket' => env('GIANESTBALLOON_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'sslsport' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('SSLSPORT_HOST', '127.0.0.1'),
            'port' => env('SSLSPORT_PORT', '3306'),
            'database' => env('SSLSPORT_DATABASE', 'forge'),
            'username' => env('SSLSPORT_USERNAME', 'forge'),
            'password' => env('SSLSPORT_PASSWORD', ''),
            'unix_socket' => env('SSLSPORT_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'pimavi' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('PIMAVI_HOST', '127.0.0.1'),
            'port' => env('PIMAVI_PORT', '3306'),
            'database' => env('PIMAVI_DATABASE', 'forge'),
            'username' => env('PIMAVI_USERNAME', 'forge'),
            'password' => env('PIMAVI_PASSWORD', ''),
            'unix_socket' => env('PIMAVI_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'catialamarl' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('CATIALAMARL_HOST', '127.0.0.1'),
            'port' => env('CATIALAMARL_PORT', '3306'),
            'database' => env('CATIALAMARL_DATABASE', 'forge'),
            'username' => env('CATIALAMARL_USERNAME', 'forge'),
            'password' => env('CATIALAMARL_PASSWORD', ''),
            'unix_socket' => env('CATIALAMARL_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'inversionespv' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('INVERSIONESPV_HOST', '127.0.0.1'),
            'port' => env('INVERSIONESPV_PORT', '3306'),
            'database' => env('INVERSIONESPV_DATABASE', 'forge'),
            'username' => env('INVERSIONESPV_USERNAME', 'forge'),
            'password' => env('INVERSIONESPV_PASSWORD', ''),
            'unix_socket' => env('INVERSIONESPV_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],      
        'arviconsult' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('ARVICONSULT_HOST', '127.0.0.1'),
            'port' => env('ARVICONSULT_PORT', '3306'),
            'database' => env('ARVICONSULT_DATABASE', 'forge'),
            'username' => env('ARVICONSULT_USERNAME', 'forge'),
            'password' => env('ARVICONSULT_PASSWORD', ''),
            'unix_socket' => env('ARVICONSULT_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],      
        'demo' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DEMO_HOST', '127.0.0.1'),
            'port' => env('DEMO_PORT', '3306'),
            'database' => env('DEMO_DATABASE', 'forge'),
            'username' => env('DEMO_USERNAME', 'forge'),
            'password' => env('DEMO_PASSWORD', ''),
            'unix_socket' => env('DEMO_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],  
        'mancent' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('MANCENT_HOST', '127.0.0.1'),
            'port' => env('MANCENT_PORT', '3306'),
            'database' => env('MANCENT_DATABASE', 'forge'),
            'username' => env('MANCENT_USERNAME', 'forge'),
            'password' => env('MANCENT_PASSWORD', ''),
            'unix_socket' => env('MANCENT_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],  
        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
