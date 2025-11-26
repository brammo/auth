<?php
declare(strict_types=1);

/**
 * Test App configuration
 */

return [
    'debug' => true,
    
    'App' => [
        'namespace' => 'TestApp',
        'encoding' => 'UTF-8',
        'defaultLocale' => 'en_US',
        'defaultTimezone' => 'UTC',
        'base' => false,
        'dir' => 'src',
        'webroot' => 'webroot',
        'wwwRoot' => WWW_ROOT,
        'fullBaseUrl' => 'http://localhost',
        'imageBaseUrl' => 'img/',
        'cssBaseUrl' => 'css/',
        'jsBaseUrl' => 'js/',
        'paths' => [
            'plugins' => [dirname(dirname(__DIR__)) . DS],
            'templates' => [dirname(__DIR__) . DS . 'templates' . DS],
            'locales' => [dirname(__DIR__) . DS . 'locale' . DS],
        ],
    ],

    'Error' => [
        'errorLevel' => E_ALL,
        'exceptionRenderer' => 'Cake\Error\ExceptionRenderer',
        'skipLog' => [],
        'log' => true,
        'trace' => true,
        'ignoredDeprecationPaths' => [
            // Since 3.3.0: loadIdentifier() usage is deprecated. Directly pass `'identifier'` config to the Authenticator.
            // but still used in CakePHP AuthenticationService
            'vendor/cakephp/authentication/src/AuthenticationService.php'
        ],
    ],
];
