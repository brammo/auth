<?php
/**
 * Authentication configuration
 * 
 * This file contains configuration settings for user authentication,
 * including user table, routes, authentication fields, and templates.
 */

use Cake\Core\Configure;
use Cake\Utility\Hash;

// debug("Loading Brammo/Auth auth configuration.");

$config = [

        /**
         * User settings
         */
        'Users' => [

            /**
             * Table used to manage users
             * It is expected to have fields like 'email' and 'password'.
             */
            'table' => 'Brammo/Auth.Users',

            /** 
             * Controller used to manage user authentication
             * It should have methods like login() and logout().
             */
            'controller' => 'Brammo/Auth.User',
        ],

        /**
         * Routes settings
         */
        'Routes' => [

            /** 
             * Login route
             * The URL where users are redirected to log in.
             */
            'login' => '/login',

            /**
             * Logout route
             * The URL where users are redirected to log out.
             */
            'logout' => '/logout',

            /**
             * Default redirect after login
             * The URL where users are redirected after successful login.
             */
            'loginRedirect' => '/',
        ],

        /**
         * Authentication settings
         * Configuration for authentication fields and password hashing.
         */
        'Authentication' => [

            /**
             * Fields used for authentication
             * Specifies which fields represent username and password.
             */
            'fields' => [
                'username' => 'email',
                'password' => 'password'
            ],

            /**
             * Password hasher configuration
             * 
             * Specifies the class used for hashing passwords.
             * 
             * For multiple hashers (e.g., for legacy support), use:
             * ```
             * 'className' => 'Authentication.Fallback',
             *   'hashers' => [
             *       'Authentication.Default',
             *       [
             *           'className' => 'Authentication.Legacy',
             *           'hashType' => 'sha1',
             *       ],
             *   ]
             * ```
             */
            'passwordHasher' => [
                'className' => 'Authentication.Default'
            ],
        ],

        /**
         * Template settings
         * Configuration for view templates used in authentication.
         */
        'Templates' => [

            /**
             * Login template
             * The view template used for rendering the login form.
             */
            'login' => 'Brammo/Auth.User/login',
        ],
];

// As the plugin loads last, we should merge with existing configuration
// to allow application-level overrides.

// Read existing 'Auth' configuration
$currentConfig = Configure::read('Auth');

// Merge with current configuration if it exists
if (is_array($currentConfig)) {
    $config = Hash::merge($config, $currentConfig);
}

return [
    'Auth' => $config
];
