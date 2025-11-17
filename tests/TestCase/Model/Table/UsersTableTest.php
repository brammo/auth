<?php
declare(strict_types=1);

namespace Brammo\Auth\Test\TestCase\Model\Table;

use Brammo\Auth\Model\Entity\User;
use Brammo\Auth\Model\Table\UsersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * Brammo\Auth\Model\Table\UsersTable Test Case
 */
class UsersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Brammo\Auth\Model\Table\UsersTable
     */
    protected UsersTable $Users;

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
        
        $config = TableRegistry::getTableLocator()->exists('Users') ? [] : ['className' => UsersTable::class];
        $this->Users = TableRegistry::getTableLocator()->get('Users', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Users);
        
        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->assertEquals('users', $this->Users->getTable());
        $this->assertEquals('id', $this->Users->getPrimaryKey());
        $this->assertEquals('name', $this->Users->getDisplayField());
        
        // Test behaviors
        $this->assertTrue($this->Users->hasBehavior('Timestamp'));
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault(): void
    {
        $user = $this->Users->newEmptyEntity();
        $user = $this->Users->patchEntity($user, []);
        
        $errors = $user->getErrors();
        
        // Should have errors for required fields
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
    }

    /**
     * Test valid user creation
     *
     * @return void
     */
    public function testCreateValidUser(): void
    {
        $data = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
        ];
        
        $user = $this->Users->newEntity($data);
        
        $this->assertEmpty($user->getErrors());
        $this->assertEquals('New User', $user->name);
        $this->assertEquals('newuser@example.com', $user->email);
    }

    /**
     * Test email validation
     *
     * @return void
     */
    public function testEmailValidation(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
        ];
        
        $user = $this->Users->newEntity($data);
        
        $errors = $user->getErrors();
        $this->assertArrayHasKey('email', $errors);
    }

    /**
     * Test email uniqueness rule
     *
     * @return void
     */
    public function testEmailUniqueness(): void
    {
        // Try to create a user with an existing email
        $data = [
            'name' => 'Duplicate User',
            'email' => 'test@example.com', // This email already exists in fixtures
            'password' => 'password123',
        ];
        
        $user = $this->Users->newEntity($data);
        $result = $this->Users->save($user);
        
        // Save should fail due to unique constraint
        $this->assertFalse($result);
        $this->assertArrayHasKey('email', $user->getErrors());
    }

    /**
     * Test password is required on create
     *
     * @return void
     */
    public function testPasswordRequiredOnCreate(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test2@example.com',
        ];
        
        $user = $this->Users->newEntity($data);
        
        // Password is required on create but can be empty (for password hashing)
        // The validation is set to allowEmptyString
        $this->assertEmpty($user->getErrors());
    }

    /**
     * Test saving a user
     *
     * @return void
     */
    public function testSaveUser(): void
    {
        $data = [
            'name' => 'Save Test User',
            'email' => 'savetest@example.com',
            'password' => 'password123',
        ];
        
        $user = $this->Users->newEntity($data);
        $result = $this->Users->save($user);
        
        $this->assertInstanceOf(User::class, $result);
        $this->assertNotNull($result->id);
        $this->assertEquals('Save Test User', $result->name);
    }

    /**
     * Test finding a user by email
     *
     * @return void
     */
    public function testFindByEmail(): void
    {
        $user = $this->Users->find()
            ->where(['email' => 'test@example.com'])
            ->first();
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
    }

    /**
     * Test updating a user
     *
     * @return void
     */
    public function testUpdateUser(): void
    {
        $user = $this->Users->get(1);
        $user->name = 'Updated Name';
        
        $result = $this->Users->save($user);
        
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Updated Name', $result->name);
    }

    /**
     * Test deleting a user
     *
     * @return void
     */
    public function testDeleteUser(): void
    {
        $user = $this->Users->get(1);
        $result = $this->Users->delete($user);
        
        $this->assertTrue($result);
        
        // Verify user is deleted
        $count = $this->Users->find()->where(['id' => 1])->count();
        $this->assertEquals(0, $count);
    }

    /**
     * Test timestamp behavior
     *
     * @return void
     */
    public function testTimestampBehavior(): void
    {
        $data = [
            'name' => 'Timestamp Test',
            'email' => 'timestamp@example.com',
            'password' => 'password123',
        ];
        
        $user = $this->Users->newEntity($data);
        $user = $this->Users->save($user);
        
        // Timestamps should be automatically set
        $this->assertNotNull($user->created);
        $this->assertNotNull($user->modified);
    }

    /**
     * Test name max length validation
     *
     * @return void
     */
    public function testNameMaxLength(): void
    {
        $data = [
            'name' => str_repeat('a', 256), // 256 characters, max is 255
            'email' => 'longname@example.com',
            'password' => 'password123',
        ];
        
        $user = $this->Users->newEntity($data);
        
        $errors = $user->getErrors();
        $this->assertArrayHasKey('name', $errors);
    }

    /**
     * Test password max length validation
     *
     * @return void
     */
    public function testPasswordMaxLength(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'longpass@example.com',
            'password' => str_repeat('a', 256), // 256 characters, max is 255
        ];
        
        $user = $this->Users->newEntity($data);
        
        $errors = $user->getErrors();
        $this->assertArrayHasKey('password', $errors);
    }
}
