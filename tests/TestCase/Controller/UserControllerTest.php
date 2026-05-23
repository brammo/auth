<?php
declare(strict_types=1);

namespace Brammo\Auth\Test\TestCase\Controller;

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
        Configure::write('Auth.Authentication.finder', 'active');
        Configure::write('Auth.Messages', [
            'invalidCredentials' => 'Invalid email or password',
            'blocked' => 'Your account has been blocked',
            'notActivated' => 'Your account is not yet activated',
            'enumerateAccounts' => true,
        ]);

        $this->enableRetainFlashMessages();
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

        $this->assertRedirectContains('/login');
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

    /**
     * Test login with blocked user shows blocked message
     *
     * @return void
     */
    public function testLoginWithBlockedUser(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $this->post('/login', [
            'email' => 'blocked@example.com',
            'password' => 'password',
        ]);

        $this->assertResponseOk();
        $this->assertNoRedirect();
        $this->assertFlashMessage('Your account has been blocked');
    }

    /**
     * Test login with new (not activated) user shows not activated message
     *
     * @return void
     */
    public function testLoginWithNewUser(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $this->post('/login', [
            'email' => 'new@example.com',
            'password' => 'password',
        ]);

        $this->assertResponseOk();
        $this->assertNoRedirect();
        $this->assertFlashMessage('Your account is not yet activated');
    }

    /**
     * Test login with active user succeeds
     *
     * @return void
     */
    public function testLoginWithActiveUser(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Should authenticate successfully and redirect
        $this->assertRedirect('/');
    }

    /**
     * Test blocked user with wrong password shows invalid credentials
     *
     * @return void
     */
    public function testBlockedUserWithWrongPassword(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $this->post('/login', [
            'email' => 'blocked@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertResponseOk();
        $this->assertNoRedirect();
        $this->assertFlashMessage('Your account has been blocked');
    }

    /**
     * Test enumerateAccounts disabled shows generic message for blocked user
     *
     * @return void
     */
    public function testEnumerateAccountsDisabled(): void
    {
        Configure::write('Auth.Messages.enumerateAccounts', false);

        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $this->post('/login', [
            'email' => 'blocked@example.com',
            'password' => 'password',
        ]);

        $this->assertResponseOk();
        $this->assertNoRedirect();
        $this->assertFlashMessage('Invalid email or password');
    }

    /**
     * Test that password is rehashed after successful login when rehashPasswords is enabled
     *
     * @return void
     */
    public function testPasswordRehashAfterLoginWhenEnabled(): void
    {
        // Enable password rehashing
        Configure::write('Auth.Authentication.rehashPasswords', true);

        // Create a password hash with a lower cost factor that will need rehashing
        $lowCostHash = password_hash('password', PASSWORD_BCRYPT, ['cost' => 4]);

        // Update the test user's password with the low-cost hash directly in database
        // to bypass the entity's password hashing mutator
        $Users = $this->getTableLocator()->get('Brammo/Auth.Users');
        $connection = $Users->getConnection();
        $connection->update('users', ['password' => $lowCostHash], ['id' => 1]);

        // Verify the low-cost hash is saved
        $originalHash = $Users->get(1)->password;
        $this->assertSame($lowCostHash, $originalHash);

        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertRedirect('/');

        // After successful login, the password should have been rehashed
        $updatedUser = $Users->get(1);
        $newHash = $updatedUser->password;

        // The hash should be different (rehashed with default cost)
        $this->assertNotSame($originalHash, $newHash);

        // The new hash should still validate the same password
        $this->assertTrue(password_verify('password', $newHash));
    }

    /**
     * Test that password is NOT rehashed after successful login when rehashPasswords is disabled
     *
     * @return void
     */
    public function testPasswordNotRehashAfterLoginWhenDisabled(): void
    {
        // Ensure password rehashing is disabled (default)
        Configure::write('Auth.Authentication.rehashPasswords', false);

        // Create a password hash with a lower cost factor that would need rehashing
        $lowCostHash = password_hash('password', PASSWORD_BCRYPT, ['cost' => 4]);

        // Update the test user's password with the low-cost hash directly in database
        // to bypass the entity's password hashing mutator
        $Users = $this->getTableLocator()->get('Brammo/Auth.Users');
        $connection = $Users->getConnection();
        $connection->update('users', ['password' => $lowCostHash], ['id' => 1]);

        // Verify the low-cost hash is saved
        $originalHash = $Users->get(1)->password;
        $this->assertSame($lowCostHash, $originalHash);

        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertRedirect('/');

        // After successful login, the password should NOT have been rehashed
        $updatedUser = $Users->get(1);
        $newHash = $updatedUser->password;

        // The hash should remain the same (not rehashed)
        $this->assertSame($originalHash, $newHash);
    }

    /**
     * Test that password is NOT rehashed by default (rehashPasswords not configured)
     *
     * @return void
     */
    public function testPasswordNotRehashByDefault(): void
    {
        // Do not set rehashPasswords - test default behavior

        // Create a password hash with a lower cost factor that would need rehashing
        $lowCostHash = password_hash('password', PASSWORD_BCRYPT, ['cost' => 4]);

        // Update the test user's password with the low-cost hash directly in database
        // to bypass the entity's password hashing mutator
        $Users = $this->getTableLocator()->get('Brammo/Auth.Users');
        $connection = $Users->getConnection();
        $connection->update('users', ['password' => $lowCostHash], ['id' => 1]);

        // Verify the low-cost hash is saved
        $originalHash = $Users->get(1)->password;
        $this->assertSame($lowCostHash, $originalHash);

        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertRedirect('/');

        // After successful login, the password should NOT have been rehashed (default behavior)
        $updatedUser = $Users->get(1);
        $newHash = $updatedUser->password;

        // The hash should remain the same (not rehashed by default)
        $this->assertSame($originalHash, $newHash);
    }
}
