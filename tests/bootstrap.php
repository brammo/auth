<?php
declare(strict_types=1);

/**
 * Test suite bootstrap for Brammo/Auth plugin
 */

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\Fixture\SchemaLoader;

// Load composer autoloader
$root = dirname(__DIR__);
require_once $root . '/vendor/autoload.php';

// Load CakePHP functions
require_once $root . '/vendor/cakephp/cakephp/src/functions.php';

// Define constants
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

define('ROOT', $root);
define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'vendor' . DS . 'cakephp' . DS . 'cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . 'src' . DS);
define('APP', ROOT . DS . 'tests' . DS . 'test_app' . DS);
define('APP_DIR', 'test_app');
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', APP . 'webroot' . DS);
define('TMP', sys_get_temp_dir() . DS);
define('CONFIG', APP . 'config' . DS);
define('CACHE', TMP . 'cache' . DS);
define('LOGS', TMP . 'logs' . DS);
define('SESSIONS', TMP . 'sessions' . DS);

// Create cache and log directories if they don't exist
$dirs = [CACHE, LOGS, SESSIONS];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Configure paths
Configure::write('App', [
    'namespace' => 'TestApp',
    'encoding' => 'UTF-8',
    'defaultLocale' => 'en_US',
    'defaultTimezone' => 'UTC',
    'paths' => [
        'plugins' => [ROOT . DS . 'vendor' . DS, ROOT . DS],
        'templates' => [APP . 'templates' . DS],
    ],
]);

Configure::write('debug', true);

// Configure security salt for testing
use Cake\Utility\Security;

Security::setSalt('__REPLACE_WITH_LONG_RANDOM_STRING_FOR_TESTING__1234567890abcdef');

// Configure cache
use Cake\Cache\Cache;

Cache::setConfig('_cake_core_', [
    'className' => 'File',
    'path' => CACHE,
]);

Cache::setConfig('_cake_model_', [
    'className' => 'File',
    'path' => CACHE,
]);

// Setup SQLite test database
$config = [
    'url' => 'sqlite:///:memory:',
    'timezone' => 'UTC',
    'quoteIdentifiers' => false,
    'cacheMetadata' => true,
];

try {
    ConnectionManager::drop('test');
} catch (Exception $e) {
    // ignore
}

ConnectionManager::setConfig('test', $config);
ConnectionManager::alias('test', 'default');

// Create users table for tests
$connection = ConnectionManager::get('test');
$sql = <<<SQL
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created DATETIME,
    modified DATETIME
)
SQL;
$connection->execute($sql);

// Load test configuration
if (file_exists(CONFIG . 'app_local.php')) {
    Configure::load('app_local', 'default');
}

// Timezone
date_default_timezone_set('UTC');
