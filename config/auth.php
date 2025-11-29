<?php
/**
 * Authentication configuration
 * 
 * This file contains configuration settings for user authentication,
 * including user table, routes, authentication fields, and templates.
 */

use Cake\Core\Configure;
use Cake\Utility\Hash;

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
         * Finder used for authentication
         *
         * Specifies which finder to use when looking up users.
         * Use 'active' to only allow active users to authenticate.
         */
        'finder' => 'active',

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

        /**
         * Rehash passwords
         *
         * When enabled, passwords will be automatically rehashed
         * on login if the hashing algorithm or cost has changed.
         * This is useful when migrating from legacy hashers or
         * when updating bcrypt cost parameters.
         */
        'rehashPasswords' => false,
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

    /**
     * Flash messages
     * Configuration for flash messages displayed during authentication.
     */
    'Messages' => [

        /**
         * Invalid credentials message
         * Shown when email or password is incorrect.
         */
        'invalidCredentials' => 'Invalid email or password',

        /**
         * Account blocked message
         * Shown when a blocked user attempts to log in.
         */
        'blocked' => 'Your account has been blocked',

        /**
         * Account not activated message
         * Shown when a new/unactivated user attempts to log in.
         */
        'notActivated' => 'Your account is not yet activated',
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
