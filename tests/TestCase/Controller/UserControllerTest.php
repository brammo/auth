<?php
declare(strict_types=1);

namespace Brammo\Auth\Test\TestCase\Controller;

use Brammo\Auth\Controller\UserController;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * Brammo\Auth\Controller\UserController Test Case
 *
 * @uses \Brammo\Auth\Controller\UserController
 */
class UserControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'plugin.Brammo/Auth.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up authentication configuration
        Configure::write('Auth.Users.controller', 'Brammo/Auth.User');
        Configure::write('Auth.Users.table', 'Brammo/Auth.Users');
        Configure::write('Auth.Routes.login', '/login');
        Configure::write('Auth.Routes.logout', '/logout');
        Configure::write('Auth.Routes.loginRedirect', '/');
        Configure::write('Auth.Templates.login', 'Brammo/Auth.User/login');
        Configure::write('Auth.Authentication.fields', [
            'username' => 'email',
            'password' => 'password',
        ]);
        Configure::write('Auth.Authentication.passwordHasher', [
            'className' => 'Authentication.Default',
        ]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Configure::clear();
        parent::tearDown();
    }

    /**
     * Test login GET request displays form
     *
     * @return void
     */
    public function testLoginGet(): void
    {
        $this->get('/login');
        
        $this->assertResponseOk();
    }

    /**
     * Test login with valid credentials
     *
     * @return void
     */
    public function testLoginPostWithValidCredentials(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        
        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        
        $this->assertRedirect('/');
    }

    /**
     * Test login with invalid credentials
     *
     * @return void
     */
    public function testLoginPostWithInvalidCredentials(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        
        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);
        
        $this->assertResponseOk();
        $this->assertFlashMessage('Invalid email or password');
    }

    /**
     * Test login with redirect parameter
     *
     * @return void
     */
    public function testLoginWithRedirectParameter(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        
        $this->post('/login?redirect=/dashboard', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        
        $this->assertRedirect('/dashboard');
    }

    /**
     * Test login with custom redirect from configuration
     *
     * @return void
     */
    public function testLoginWithCustomRedirectFromConfig(): void
    {
        Configure::write('Auth.Routes.loginRedirect', '/admin/dashboard');
        
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        
        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        
        $this->assertRedirect('/admin/dashboard');
    }

    /**
     * Test logout
     *
     * @return void
     */
    public function testLogout(): void
    {
        // First login
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        
        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        
        // Then logout
        $this->get('/logout');
        
        $this->assertRedirect('/login');
    }

    /**
     * Test login is accessible without authentication
     *
     * @return void
     */
    public function testLoginIsAccessibleWithoutAuthentication(): void
    {
        $this->get('/login');
        
        $this->assertResponseOk();
        $this->assertNoRedirect();
    }

    /**
     * Test POST with empty data
     *
     * @return void
     */
    public function testLoginPostWithEmptyData(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        
        $this->post('/login', [
            'email' => '',
            'password' => '',
        ]);
        
        $this->assertResponseOk();
        $this->assertFlashMessage('Invalid email or password');
    }

    /**
     * Test login with non-existent user
     *
     * @return void
     */
    public function testLoginWithNonExistentUser(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        
        $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);
        
        $this->assertResponseOk();
        $this->assertFlashMessage('Invalid email or password');
    }
}
