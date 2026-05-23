<?php
declare(strict_types=1);

namespace Brammo\Auth\Test\TestCase;

use Brammo\Auth\AuthPlugin;
use Cake\Core\Configure;
use Cake\Http\ServerRequestFactory;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use TestApp\Application;

/**
 * Brammo\Auth\AuthPlugin Test Case
 */
class PluginTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Brammo\Auth\AuthPlugin
     */
    protected AuthPlugin $Plugin;

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
        $this->Plugin = new AuthPlugin();
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
        Router::reload();

        parent::tearDown();
    }

    /**
     * Test bootstrap method
     *
     * @return void
     */
    public function testBootstrap(): void
    {
        Configure::delete('Auth');

        $this->Plugin->bootstrap($this->app);

        $this->assertNotNull(Configure::read('Auth'));
        $this->assertEquals('Brammo/Auth.Users', Configure::read('Auth.Users.table'));
        $this->assertEquals('Brammo/Auth.User', Configure::read('Auth.Users.controller'));
        $this->assertEquals('/login', Configure::read('Auth.Routes.login'));
        $this->assertEquals('/logout', Configure::read('Auth.Routes.logout'));
        $this->assertEquals('/', Configure::read('Auth.Routes.loginRedirect'));
        $this->assertEquals('Auth', Configure::read('Auth.Authentication.sessionKey'));
        $this->assertEquals('CookieAuth', Configure::read('Auth.Authentication.cookieName'));
        $this->assertTrue(Configure::read('Auth.Messages.enumerateAccounts'));
    }

    /**
     * Test routes method registers login and logout URLs
     *
     * @return void
     */
    public function testRoutes(): void
    {
        Configure::write('Auth.Users.controller', 'Brammo/Auth.User');
        Configure::write('Auth.Routes.login', '/login');
        Configure::write('Auth.Routes.logout', '/logout');

        Router::reload();
        $routes = Router::createRouteBuilder('/');
        $this->Plugin->routes($routes);

        $loginRequest = ServerRequestFactory::fromGlobals(['REQUEST_URI' => '/login']);
        $loginRoute = Router::getRouteCollection()->parseRequest($loginRequest);
        $this->assertSame('login', $loginRoute['action']);
        $this->assertSame('User', $loginRoute['controller']);
        $this->assertSame('Brammo/Auth', $loginRoute['plugin']);

        $logoutRequest = ServerRequestFactory::fromGlobals(['REQUEST_URI' => '/logout']);
        $logoutRoute = Router::getRouteCollection()->parseRequest($logoutRequest);
        $this->assertSame('logout', $logoutRoute['action']);
        $this->assertSame('User', $logoutRoute['controller']);
        $this->assertSame('Brammo/Auth', $logoutRoute['plugin']);
    }

    /**
     * Test routes with custom configuration
     *
     * @return void
     */
    public function testRoutesWithCustomConfiguration(): void
    {
        Configure::write('Auth.Users.controller', 'Brammo/Auth.User');
        Configure::write('Auth.Routes.login', '/custom-login');
        Configure::write('Auth.Routes.logout', '/custom-logout');

        Router::reload();
        $routes = Router::createRouteBuilder('/');
        $this->Plugin->routes($routes);

        $loginRequest = ServerRequestFactory::fromGlobals(['REQUEST_URI' => '/custom-login']);
        $loginRoute = Router::getRouteCollection()->parseRequest($loginRequest);
        $this->assertSame('login', $loginRoute['action']);

        $logoutRequest = ServerRequestFactory::fromGlobals(['REQUEST_URI' => '/custom-logout']);
        $logoutRoute = Router::getRouteCollection()->parseRequest($logoutRequest);
        $this->assertSame('logout', $logoutRoute['action']);
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
