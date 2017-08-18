<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
    */



    'fetch' => PDO::FETCH_CLASS,

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
            'database' => database_path('database.sqlite'),
            'prefix' => '',
        ],
        //业务数据库
        'mysql' => [
            'driver' => 'mysql',
//            'host' => env('DB_HOST_BUSINESS', 'localhost'),
            'read' => [
                'host'  => env('DB_HOST_READ', 'localhost'),
            ],
            'write' => [
                'host'  => env('DB_HOST_BUSINESS', 'localhost'),
            ],
            'port' => env('DB_PORT_BUSINESS', '3306'),
            'database' => env('DB_DATABASE_BUSINESS', 'db_ex_business'),
            'username' => env('DB_USERNAME_BUSINESS', 'root'),
            'password' => env('DB_PASSWORD_BUSINESS', 'WorkBand2015'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        //业务数据库
        'onlyReadMysql' => [
            'driver' => 'mysql',
            'host' => env('DB_FHW_HOST_READ', 'localhost'),
//            'read' => [
//                'host'  => env('DB_HOST_READ', 'localhost'),
//            ],
//            'write' => [
//                'host'  => env('DB_HOST_BUSINESS', 'localhost'),
//            ],
            'port' => env('DB_PORT_BUSINESS', '3306'),
            'database' => env('DB_DATABASE_BUSINESS', 'db_ex_business'),
            'username' => env('DB_USERNAME_BUSINESS', 'root'),
            'password' => env('DB_PASSWORD_BUSINESS', 'WorkBand2015'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
        //配置数据库
        'mysql_config' => [
            'driver' => 'mysql',
//            'host' => env('DB_HOST', 'localhost'),
            'read' => [
                'host'  => env('DB_HOST_READ', 'localhost'),
            ],
            'write' => [
                'host'  => env('DB_HOST', 'localhost'),
            ],
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'db_ex_config'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', 'WorkBand2015'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
        //财务核算数据库
        'db_ex_finance' => [
            'driver' => 'mysql',
//            'host' => env('DB_HOST_FINANCE', 'localhost'),
            'read' => [
                'host'  => env('DB_HOST_READ', 'localhost'),
            ],
            'write' => [
                'host'  => env('DB_HOST_FINANCE', 'localhost'),
            ],
            'port' => env('DB_PORT_FINANCE', '3306'),
            'database' => env('DB_DATABASE_FINANCE', 'db_ex_finance'),
            'username' => env('DB_USERNAME_FINANCE', 'root'),
            'password' => env('DB_PASSWORD_FINANCE', 'WorkBand2015'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
        //日志数据库
        'mysql_log' => [
            'driver' => 'mysql',
//            'host' => env('DB_HOST_LOG', 'locathost'),
            'read' => [
                'host'  => env('DB_HOST_READ', 'localhost'),
            ],
            'write' => [
                'host'  => env('DB_HOST_LOG', 'localhost'),
            ],
            'port' => env('DB_PORT_LOG', '3306'),
            'database' => env('DB_DATABASE_LOG', 'db_ex_logs'),
            'username' => env('DB_USERNAME_LOG', 'root'),
            'password' => env('DB_PASSWORD_LOG', 'WorkBand2015'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
        //h5日志数据库
        'h5_log' => [
            'driver' => 'mysql',
//            'host' => env('DB_HOST_H5_LOG', 'locathost'),
            'read' => [
                'host'  => env('DB_HOST_READ', 'localhost'),
            ],
            'write' => [
                'host'  => env('DB_HOST_H5_LOG', 'localhost'),
            ],
            'port' => env('DB_PORT_H5_LOG', '3306'),
            'database' => env('DB_DATABASE_H5_LOG', 'db_ex_logs'),
            'username' => env('DB_USERNAME_H5_LOG', 'root'),
            'password' => env('DB_PASSWORD_H5_LOG', 'WorkBand2015'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
        //中间表数据库
        'mysql_stat' => [
            'driver' => 'mysql',
//            'host' => env('DB_HOST_Mid', 'locathost'),
            'read' => [
                'host'  => env('DB_HOST_READ', 'localhost'),
            ],
            'write' => [
                'host'  => env('DB_HOST_Mid', 'localhost'),
            ],
            'port' => env('DB_PORT_Mid', '3306'),
            'database' => env('DB_DATABASE_Mid', 'db_ex_stat'),
            'username' => env('DB_USERNAME_Mid', 'root'),
            'password' => env('DB_PASSWORD_Mid', 'WorkBand2015'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        //吴晓波数据库
        'mysql_kxdz' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_KXDZ', 'locathost'),
            'port' => env('DB_PORT_KXDZ', '3306'),
            'database' => env('DB_DATABASE_KXDZ', 'db_zhang_df'),
            'username' => env('DB_USERNAME_KXDZ', 'root'),
            'password' => env('DB_PASSWORD_KXDZ', 'WorkBand2015'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
        //吴晓波数据库统计
        'mysql_chain' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_WXB', 'locathost'),
            'port' => env('DB_PORT_WXB', '3306'),
            'database' => env('DB_DATABASE_WXB', 'db_zhang_df'),
            'username' => env('DB_USERNAME_WXB', 'root'),
            'password' => env('DB_PASSWORD_WXB', 'WorkBand2015'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
        //临时流量计算数据表
        'mysql_tmp_test' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_TMP_TEST', 'locathost'),
            'port' => env('DB_PORT_TMP_TEST', '3306'),
            'database' => env('DB_DATABASE_TMP_TEST', 'db_ex_stat'),
            'username' => env('DB_USERNAME_TMP_TEST', 'root'),
            'password' => env('DB_PASSWORD_TMP_TEST', 'WorkBand2015'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        //帮助文档数据库
        'mysql_document' => [
            'driver' => 'mysql',
//            'host' => env('DB_HOST_BUSINESS', 'localhost'),
            'host'  => env('DB_HOST_DOCUMENT', 'localhost'),
            'port' => env('DB_PORT_DOCUMENT', '3306'),
            'database' => env('DB_DATABASE_DOCUMENT', 'db_ex_document'),
            'username' => env('DB_USERNAME_DOCUMENT', 'root'),
            'password' => env('DB_PASSWORD_DOCUMENT', 'WorkBand2015'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        //帮助中心数据库
            'mysql_help' => [
                    'driver' => 'mysql',
//            'host' => env('DB_HOST_BUSINESS', 'localhost'),
                    'host'  => env('DB_HOST_DOC', 'localhost'),
                    'port' => env('DB_PORT_DOC', '3306'),
                    'database' => env('DB_DATABASE_DOC', 'db_help_document'),
                    'username' => env('DB_USERNAME_DOC', 'root'),
                    'password' => env('DB_PASSWORD_DOC', 'WorkBand2015'),
                    'charset' => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix' => '',
                    'strict' => false,
                    'engine' => null,
            ],

        //通知中心数据库
            'mysql_notice' => [
                    'driver' => 'mysql',
//            'host' => env('DB_HOST_BUSINESS', 'localhost'),
                    'host'  => env('DB_HOST_NOTICE', 'localhost'),
                    'port' => env('DB_PORT_NOTICE', '3306'),
                    'database' => env('DB_DATABASE_NOTICE', 'db_ex_notice'),
                    'username' => env('DB_USERNAME_NOTICE', 'root'),
                    'password' => env('DB_PASSWORD_NOTICE', 'WorkBand2015'),
                    'charset' => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix' => '',
                    'strict' => false,
                    'engine' => null,
            ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
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
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'cluster' => false,

        'default' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

        'privilege' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 1,
        ],

    ],

];
