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
        'ignoredDeprecationPaths' => [],
    ],
];
