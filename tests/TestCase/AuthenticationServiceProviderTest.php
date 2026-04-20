<?php
declare(strict_types=1);

namespace Brammo\Auth\Test\TestCase;

use Brammo\Auth\AuthenticationServiceProvider;
use Cake\Core\Configure;
use Cake\Http\ServerRequestFactory;
use Cake\TestSuite\TestCase;

/**
 * Brammo\Auth\AuthenticationServiceProvider Test Case
 */
class AuthenticationServiceProviderTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Brammo\Auth\AuthenticationServiceProvider
     */
    protected AuthenticationServiceProvider $provider;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set up authentication configuration
        Configure::write('Auth.Authentication.fields', [
            'username' => 'email',
            'password' => 'password',
        ]);
        Configure::write('Auth.Routes.login', '/login');
        Configure::write('Auth.Users.table', 'Brammo/Auth.Users');
        Configure::write('Auth.Authentication.passwordHasher', [
            'className' => 'Authentication.Default',
        ]);

        $this->provider = new AuthenticationServiceProvider();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->provider);
        Configure::clear();

        parent::tearDown();
    }

    /**
     * Test getAuthenticationService method
     *
     * @return void
     */
    public function testGetAuthenticationService(): void
    {
        $request = ServerRequestFactory::fromGlobals();

        $service = $this->provider->getAuthenticationService($request);

        $this->assertInstanceOf('Authentication\AuthenticationServiceInterface', $service);
    }

    /**
     * Test authentication service configuration
     *
     * @return void
     */
    public function testAuthenticationServiceConfiguration(): void
    {
        $request = ServerRequestFactory::fromGlobals();

        $service = $this->provider->getAuthenticationService($request);

        // Test that the service is properly configured by attempting to use it
        $result = $service->authenticate($request);

        // Should not be authenticated with empty request
        $this->assertFalse($result->isValid());
    }

    /**
     * Test authentication service with custom configuration
     *
     * @return void
     */
    public function testGetAuthenticationServiceWithCustomConfig(): void
    {
        // Set custom configuration
        Configure::write('Auth.Authentication.fields', [
            'username' => 'custom_email',
            'password' => 'custom_password',
        ]);
        Configure::write('Auth.Routes.login', '/custom-login');
        Configure::write('Auth.Users.table', 'CustomUsers');

        $request = ServerRequestFactory::fromGlobals();

        $service = $this->provider->getAuthenticationService($request);

        $this->assertInstanceOf('Authentication\AuthenticationServiceInterface', $service);
    }

    /**
     * Test authentication with fallback password hasher
     *
     * @return void
     */
    public function testAuthenticationServiceWithFallbackHasher(): void
    {
        // Configure fallback password hasher
        Configure::write('Auth.Authentication.passwordHasher', [
            'className' => 'Authentication.Fallback',
            'hashers' => [
                'Authentication.Default',
                [
                    'className' => 'Authentication.Legacy',
                    'hashType' => 'sha1',
                ],
            ],
        ]);

        $request = ServerRequestFactory::fromGlobals();

        $service = $this->provider->getAuthenticationService($request);

        $this->assertInstanceOf('Authentication\AuthenticationServiceInterface', $service);
    }

    /**
     * Test authentication service with custom session key
     *
     * @return void
     */
    public function testGetAuthenticationServiceWithCustomSessionKey(): void
    {
        Configure::write('Auth.Authentication.sessionKey', 'AdminAuth');

        $request = ServerRequestFactory::fromGlobals();

        $service = $this->provider->getAuthenticationService($request);

        $this->assertInstanceOf('Authentication\AuthenticationServiceInterface', $service);
    }

    /**
     * Test authentication service with custom cookie name
     *
     * @return void
     */
    public function testGetAuthenticationServiceWithCustomCookieName(): void
    {
        Configure::write('Auth.Authentication.cookieName', 'AdminCookieAuth');

        $request = ServerRequestFactory::fromGlobals();

        $service = $this->provider->getAuthenticationService($request);

        $this->assertInstanceOf('Authentication\AuthenticationServiceInterface', $service);
    }

    /**
     * Test authentication service uses default session key and cookie name
     *
     * @return void
     */
    public function testGetAuthenticationServiceDefaultSessionKeyAndCookieName(): void
    {
        // Ensure no custom values are set
        Configure::delete('Auth.Authentication.sessionKey');
        Configure::delete('Auth.Authentication.cookieName');

        $request = ServerRequestFactory::fromGlobals();

        $service = $this->provider->getAuthenticationService($request);

        $this->assertInstanceOf('Authentication\AuthenticationServiceInterface', $service);
    }
}
