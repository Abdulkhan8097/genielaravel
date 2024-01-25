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
            'strict' => false, //true
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                PDO::ATTR_EMULATE_PREPARES => true,
            ]) : [],
        ],

        'invdb' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_INVDB', '127.0.0.1'),
            'port' => env('DB_PORT_INVDB', '3306'),
            'database' => env('DB_DATABASE_INVDB', 'forge'),
            'username' => env('DB_USERNAME_INVDB', 'forge'),
            'password' => env('DB_PASSWORD_INVDB', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false, //true
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                PDO::ATTR_EMULATE_PREPARES => true,
            ]) : [],
        ],

        'rankmf' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('RANKMF_MYSQL_DB_HOST', ''),
            'port' => env('RANKMF_MYSQL_DB_PORT', '3306'),
            'database' => env('RANKMF_MYSQL_DB_DATABASE', 'forge'),
            'username' => env('RANKMF_MYSQL_DB_USERNAME', 'forge'),
            'password' => env('RANKMF_MYSQL_DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false, //true
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'partner-rankmf' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('PARTNERS_MYSQL_DB_HOST', ''),
            'port' => env('PARTNERS_MYSQL_DB_PORT', '3306'),
            'database' => env('PARTNERS_MYSQL_DB_DATABASE', 'forge'),
            'username' => env('PARTNERS_MYSQL_DB_USERNAME', 'forge'),
            'password' => env('PARTNERS_MYSQL_DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false, //true
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

		'mongodb' => [
			'driver' => 'mongodb',
			'dsn' => env('DB_URI', 'mongodb://'.env('MONGO_DB_USERNAME').':'.env('MONGO_DB_PASSWORD').'@'.env('MONGO_DB_HOST').':'.env('MONGO_DB_PORT').'/'.env('MONGO_DB_DATABASE').'?retryWrites=true&w=majority'),
			'database' => env('MONGO_DB_DATABASE'),
		],


		'partnermongodb' => [
			'driver' => 'mongodb',
			'dsn' => env('DB_URI', 'mongodb://'.env('PARTNERS_MONGO_DB_USERNAME').':'.env('PARTNERS_MONGO_DB_PASSWORD').'@'.env('PARTNERS_MONGO_DB_HOST').':'.env('PARTNERS_MONGO_DB_PORT').'/'.env('PARTNERS_MONGO_DB_DATABASE').'?retryWrites=true&w=majority'),
			'database' => env('PARTNERS_MONGO_DB_DATABASE'),
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

        'mfplus' => [
            'driver' => 'oracle',
            'url' => env('DATABASE_URL'),
            'host' => env('ORACLE_DB_HOST', 'localhost'),
            'port' => env('ORACLE_DB_PORT', '1521'),
            'database' => env('ORACLE_DB_DATABASE', 'forge'),
            'username' => env('ORACLE_DB_USERNAME', 'forge'),
            'password' => env('ORACLE_DB_PASSWORD', ''),
            'service_name' => env('ORACLE_DB_SERVICE_NAME', ''),
            'prefix' => env('ORACLE_DB_PREFIX', ''),
            'prefix_schema' => env('ORACLE_DB_PREFIX_SCHEMA', ''),
            'charset' => 'utf8',
            'options' => array(
                \PDO::ATTR_PERSISTENT => true,
                \PDO::ATTR_CASE,
                \PDO::CASE_LOWER,
                \PDO::ATTR_ERRMODE,
                \PDO::ERRMODE_EXCEPTION,
            ),
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
