<?php
declare(strict_types=1);

namespace Brammo\Auth\Test\TestCase;

use Authentication\AuthenticationService;
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
        Configure::delete('Auth.Authentication.sessionKey');
        Configure::delete('Auth.Authentication.cookieName');

        $request = ServerRequestFactory::fromGlobals();

        $service = $this->provider->getAuthenticationService($request);
        $this->assertInstanceOf(AuthenticationService::class, $service);

        $authenticators = $service->getConfig('authenticators');
        $this->assertSame('Auth', $authenticators['Authentication.Session']['sessionKey']);
        $this->assertSame('CookieAuth', $authenticators['Authentication.Cookie']['cookie']['name']);
        $this->assertSame('remember_me', $authenticators['Authentication.Cookie']['rememberMeField']);
    }

    /**
     * Test custom session key and cookie name are applied to authenticators
     *
     * @return void
     */
    public function testAuthenticatorConfigUsesCustomSessionAndCookie(): void
    {
        Configure::write('Auth.Authentication.sessionKey', 'AdminAuth');
        Configure::write('Auth.Authentication.cookieName', 'AdminCookieAuth');
        Configure::write('Auth.Authentication.rememberMeField', 'stay_logged_in');

        $request = ServerRequestFactory::fromGlobals();
        $service = $this->provider->getAuthenticationService($request);
        $this->assertInstanceOf(AuthenticationService::class, $service);

        $authenticators = $service->getConfig('authenticators');
        $this->assertSame('AdminAuth', $authenticators['Authentication.Session']['sessionKey']);
        $this->assertSame('AdminCookieAuth', $authenticators['Authentication.Cookie']['cookie']['name']);
        $this->assertSame('stay_logged_in', $authenticators['Authentication.Cookie']['rememberMeField']);
    }

    /**
     * Test authentication finder is passed to the password identifier
     *
     * @return void
     */
    public function testAuthenticationFinderConfiguration(): void
    {
        Configure::write('Auth.Authentication.finder', 'active');

        $request = ServerRequestFactory::fromGlobals();
        $service = $this->provider->getAuthenticationService($request);
        $this->assertInstanceOf(AuthenticationService::class, $service);

        $identifier = $service->getConfig('authenticators')['Authentication.Form']['identifier'];
        $passwordConfig = $identifier['Authentication.Password'];
        $resolver = $passwordConfig['resolver'];

        $this->assertSame('active', $resolver['finder']);
        $this->assertSame('Brammo/Auth.Users', $resolver['userModel']);
    }

    /**
     * Test cookie authenticator receives remember-me field configuration when loaded
     *
     * @return void
     */
    public function testCookieAuthenticatorRememberMeField(): void
    {
        Configure::write('Auth.Authentication.rememberMeField', 'stay_logged_in');

        $request = ServerRequestFactory::fromGlobals();
        $service = $this->provider->getAuthenticationService($request);
        $this->assertInstanceOf(AuthenticationService::class, $service);

        $cookie = $service->loadAuthenticator('Authentication.Cookie');
        $this->assertSame('stay_logged_in', $cookie->getConfig('rememberMeField'));
    }
}
