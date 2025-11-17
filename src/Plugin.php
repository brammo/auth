<?php
declare(strict_types=1);

namespace Brammo\Auth;

use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Routing\RouteBuilder;

/**
 * Plugin class
 * 
 * Provides authentication services for CakePHP applications.
 * It includes user management, authentication routes, and configuration settings.
 */
class Plugin extends BasePlugin
{
    /**
     * Plugin bootstrap method
     *
     * @param \Cake\Core\PluginApplicationInterface $app The application instance
     * @return void
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);

        // Load plugin configuration
        // The configuration file is located at config/auth.php
        Configure::load('Brammo/Auth.auth');
    }

    /**
     * Add routes for the plugin
     *
     * @param \Cake\Routing\RouteBuilder $routes The route builder to update
     * @return void
     */
    public function routes(RouteBuilder $routes): void
    {
        parent::routes($routes);

        // Controller for user authentication
        // It is defined in the configuration file.
        // For example, 'Brammo/Auth.User' for the user authentication controller.
        // It is expected to have methods like login() and logout() to manage user sessions.
        $controller = Configure::read('Auth.Users.controller');
        
        // Connect login route
        $loginRoute = Configure::read('Auth.Routes.login', '/login');
        $routes->connect($loginRoute, $controller . '::login');
        
        // Connect logout route
        $logoutRoute = Configure::read('Auth.Routes.logout', '/logout');
        $routes->connect($logoutRoute, $controller . '::logout');
    }
}
