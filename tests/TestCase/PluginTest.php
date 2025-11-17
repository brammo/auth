<?php
declare(strict_types=1);

namespace Brammo\Auth\Test\TestCase;

use Brammo\Auth\Plugin;
use Cake\Core\Configure;
use Cake\Core\Plugin as CorePlugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use TestApp\Application;

/**
 * Brammo\Auth\Plugin Test Case
 */
class PluginTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Brammo\Auth\Plugin
     */
    protected Plugin $Plugin;

    /**
     * Test application
     *
     * @var \TestApp\Application
     */
    protected Application $app;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->app = new Application(CONFIG);
        $this->Plugin = new Plugin();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Plugin, $this->app);
        Configure::clear();
        
        parent::tearDown();
    }

    /**
     * Test bootstrap method
     *
     * @return void
     */
    public function testBootstrap(): void
    {
        // Clear any existing configuration
        Configure::delete('Auth');
        
        // Bootstrap the plugin
        $this->Plugin->bootstrap($this->app);
        
        // Verify that configuration was loaded
        $this->assertNotNull(Configure::read('Auth'));
        $this->assertEquals('Brammo/Auth.Users', Configure::read('Auth.Users.table'));
        $this->assertEquals('Brammo/Auth.User', Configure::read('Auth.Users.controller'));
        $this->assertEquals('/login', Configure::read('Auth.Routes.login'));
        $this->assertEquals('/logout', Configure::read('Auth.Routes.logout'));
        $this->assertEquals('/', Configure::read('Auth.Routes.loginRedirect'));
    }

    /**
     * Test routes method
     *
     * @return void
     */
    public function testRoutes(): void
    {
        // Set up configuration
        Configure::write('Auth.Users.controller', 'Brammo/Auth.User');
        Configure::write('Auth.Routes.login', '/login');
        Configure::write('Auth.Routes.logout', '/logout');
        
        // Create a route builder
        Router::reload();
        $routes = Router::createRouteBuilder('/');
        
        // Call the routes method
        $this->Plugin->routes($routes);
        
        // Parse the routes
        $routes->scope('/', function (RouteBuilder $builder): void {
            // Additional routes can be added here
        });
        
        // Verify routes were added
        $url = Router::url(['controller' => 'User', 'action' => 'login', 'plugin' => 'Brammo/Auth']);
        $this->assertIsString($url);
    }

    /**
     * Test routes with custom configuration
     *
     * @return void
     */
    public function testRoutesWithCustomConfiguration(): void
    {
        // Set up custom configuration
        Configure::write('Auth.Users.controller', 'Brammo/Auth.User');
        Configure::write('Auth.Routes.login', '/custom-login');
        Configure::write('Auth.Routes.logout', '/custom-logout');
        
        // Create a route builder
        Router::reload();
        $routes = Router::createRouteBuilder('/');
        
        // Call the routes method
        $this->Plugin->routes($routes);
        
        // Verify configuration was read correctly
        $this->assertEquals('/custom-login', Configure::read('Auth.Routes.login'));
        $this->assertEquals('/custom-logout', Configure::read('Auth.Routes.logout'));
    }

    /**
     * Test plugin name
     *
     * @return void
     */
    public function testGetName(): void
    {
        $this->assertEquals('Brammo/Auth', $this->Plugin->getName());
    }
}
