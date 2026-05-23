<?php
declare(strict_types=1);

/**
 * Test suite bootstrap for Brammo/Auth plugin
 */

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Security;

// Load composer autoloader
$root = dirname(__DIR__);
require_once $root . '/vendor/autoload.php';

// Load CakePHP functions
require_once $root . '/vendor/cakephp/cakephp/src/functions.php';

// Define constants
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

// Configure paths
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

/*
 * Initializes default Config store and loads the main configuration file
 */

Configure::config('default', new PhpConfig());
Configure::load('app', 'default', false);

// Configure security salt for testing
Security::setSalt('__REPLACE_WITH_LONG_RANDOM_STRING_FOR_TESTING__1234567890abcdef');

Cache::setConfig('_cake_translations_', [
    'className' => 'File',
    'path' => CACHE,
]);

Cache::setConfig('_cake_model_', [
    'className' => 'File',
    'path' => CACHE,
]);

// Setup SQLite test database
$testDbPath = TMP . 'test.sqlite';
if (file_exists($testDbPath)) {
    unlink($testDbPath);
}

$config = [
    'url' => 'sqlite:///' . $testDbPath,
    'timezone' => 'UTC',
    'quoteIdentifiers' => false,
    'cacheMetadata' => false,
];

try {
    ConnectionManager::drop('test');
} catch (Exception $e) {
    // ignore
}

ConnectionManager::setConfig('test', $config);
ConnectionManager::alias('test', 'default');

/**
 * Create database schema from fixture field definitions.
 *
 * This ensures test schema stays in sync with fixtures and avoids
 * duplicating schema definitions. The fields array format matches
 * what CakePHP fixtures use.
 */
$connection = ConnectionManager::get('test');

// Users table schema - matches UsersFixture::$fields
$usersSchema = new TableSchema('users');
$usersSchema->addColumn('id', ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'autoIncrement' => true]);
$usersSchema->addColumn('name', ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null]);
$usersSchema->addColumn('email', ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null]);
$usersSchema->addColumn('password', ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null]);
$usersSchema->addColumn('status', ['type' => 'string', 'length' => 20, 'null' => false, 'default' => 'new']);
$usersSchema->addColumn('created', ['type' => 'datetime', 'null' => true, 'default' => null]);
$usersSchema->addColumn('modified', ['type' => 'datetime', 'null' => true, 'default' => null]);
$usersSchema->addConstraint('primary', ['type' => 'primary', 'columns' => ['id']]);

foreach ($usersSchema->createSql($connection) as $sql) {
    $connection->execute($sql);
}

// Load test configuration
if (file_exists(CONFIG . 'app_local.php')) {
    Configure::load('app_local', 'default');
}

// Timezone
date_default_timezone_set('UTC');
