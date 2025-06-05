<?php

use Symfony\Component\Dotenv\Dotenv;

mb_internal_encoding('UTF-8');
error_reporting(E_ALL);

define('PATH_ROOT', realpath(__DIR__ .'/../'));

$dotenv = new Dotenv();
$dotenv->load(PATH_ROOT .'/.env');

function env($k, $def=null) {
    return isset($_SERVER[$k]) ? $_SERVER[$k] : $def;
}

/**
 * Phân tích URI kết nối cơ sở dữ liệu và trích xuất thông tin.
 *
 * @param string $uri Chuỗi URI của cơ sở dữ liệu (ví dụ: "mysqli://username:password@host:port/database")
 * @return array Mảng chứa thông tin kết nối (driver, username, password, host, port, database)
 * @throws Exception Nếu không thể phân tích chuỗi URI
 */
function parse_database_uri($uri) {
    // Phân tích chuỗi URI
    $parts = parse_url($uri);

    if ($parts === false) {
        throw new Exception("Không thể phân tích chuỗi URI Database.");
    }

    // Trích xuất các thông tin từ URI
    $databaseInfo = [
        'driver' => isset($parts['scheme']) ? $parts['scheme'] : null,
        'username' => isset($parts['user']) ? $parts['user'] : null,
        'password' => isset($parts['pass']) ? urldecode($parts['pass']) : null,
        'host' => isset($parts['host']) ? $parts['host'] : null,
        'port' => isset($parts['port']) ? $parts['port'] : null,
        'database' => isset($parts['path']) ? ltrim($parts['path'], '/') : null
    ];

    return $databaseInfo;
}

$databaseInfo = parse_database_uri(env("DATABASE_MAIN"));

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_environment' => 'master',
        'master'              => [
            'adapter'   => 'mysql',
            'host'      => $databaseInfo['host'],
            'name'      => $databaseInfo['database'],
            'user'      => $databaseInfo['username'],
            'pass'      => $databaseInfo['password'],
            'port'      => $databaseInfo['port'],
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
        ],
    ],
    'version_order' => 'creation',
    'templates'=> [
        'file'=> 'templates/Migration.template.php.dist',
        'seedFile'=> 'templates/Seed.template.php.dist'
    ]
];
