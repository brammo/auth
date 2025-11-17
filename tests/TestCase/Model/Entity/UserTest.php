<?php
declare(strict_types=1);

namespace Brammo\Auth\Test\TestCase\Model\Entity;

use Brammo\Auth\Model\Entity\User;
use Cake\TestSuite\TestCase;

/**
 * Brammo\Auth\Model\Entity\User Test Case
 */
class UserTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Brammo\Auth\Model\Entity\User
     */
    protected User $User;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->User = new User([
            'id' => 1,
            'email' => 'test@example.com',
            'password' => 'hashedpassword',
            'name' => 'Test User',
        ]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->User);
        
        parent::tearDown();
    }

    /**
     * Test accessible fields
     *
     * @return void
     */
    public function testAccessibleFields(): void
    {
        $user = new User();
        
        // Test that accessible fields can be set
        $this->assertTrue($user->isAccessible('email'));
        $this->assertTrue($user->isAccessible('password'));
        $this->assertTrue($user->isAccessible('name'));
        $this->assertTrue($user->isAccessible('created'));
        $this->assertTrue($user->isAccessible('modified'));
        
        // Test that id is not accessible by default
        $this->assertFalse($user->isAccessible('id'));
    }

    /**
     * Test hidden fields
     *
     * @return void
     */
    public function testHiddenFields(): void
    {
        $array = $this->User->toArray();
        
        // Password should be hidden
        $this->assertArrayNotHasKey('password', $array);
        
        // Other fields should be present
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('email', $array);
        $this->assertArrayHasKey('name', $array);
    }

    /**
     * Test JSON serialization
     *
     * @return void
     */
    public function testJsonSerialization(): void
    {
        $json = json_encode($this->User);
        $data = json_decode($json, true);
        
        // Password should not be in JSON
        $this->assertArrayNotHasKey('password', $data);
        
        // Other fields should be present
        $this->assertEquals(1, $data['id']);
        $this->assertEquals('test@example.com', $data['email']);
        $this->assertEquals('Test User', $data['name']);
    }

    /**
     * Test entity properties
     *
     * @return void
     */
    public function testEntityProperties(): void
    {
        $this->assertEquals(1, $this->User->id);
        $this->assertEquals('test@example.com', $this->User->email);
        $this->assertEquals('hashedpassword', $this->User->password);
        $this->assertEquals('Test User', $this->User->name);
    }

    /**
     * Test mass assignment
     *
     * @return void
     */
    public function testMassAssignment(): void
    {
        $data = [
            'email' => 'new@example.com',
            'name' => 'New User',
            'password' => 'newpassword',
        ];
        
        $user = new User($data);
        
        $this->assertEquals('new@example.com', $user->email);
        $this->assertEquals('New User', $user->name);
        $this->assertEquals('newpassword', $user->password);
    }

    /**
     * Test that id cannot be mass assigned by default
     *
     * @return void
     */
    public function testIdNotMassAssignable(): void
    {
        $data = [
            'id' => 999,
            'email' => 'test@example.com',
            'name' => 'Test User',
        ];
        
        $user = new User($data, ['guard' => true]);
        
        // ID should not be set via mass assignment when guard is enabled
        // Note: CakePHP allows setting it unless guard is true
        $this->assertNull($user->id);
    }

    /**
     * Test setting individual properties
     *
     * @return void
     */
    public function testSetIndividualProperties(): void
    {
        $this->User->email = 'updated@example.com';
        $this->User->name = 'Updated Name';
        
        $this->assertEquals('updated@example.com', $this->User->email);
        $this->assertEquals('Updated Name', $this->User->name);
    }
}
