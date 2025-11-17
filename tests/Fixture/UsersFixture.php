<?php
declare(strict_types=1);

namespace Brammo\Auth\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'users';

    /**
     * Fields
     *
     * @var array<string, mixed>
     */
    public array $fields = [
        'id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'autoIncrement' => true],
        'name' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null],
        'email' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null],
        'password' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null],
        'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
        'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_general_ci',
        ],
    ];

    /**
     * Records
     *
     * @var array<array<string, mixed>>
     */
    public array $records = [
        [
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            // Password: 'password' hashed with default hasher
            'password' => '$2y$10$u05j8FjsvLBNdfhBhc21LOuVMpzpabVXQ9OpC2wO3pSO0q6t7HHMO',
            'created' => '2023-01-01 00:00:00',
            'modified' => '2023-01-01 00:00:00',
        ],
        [
            'id' => 2,
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            // Password: 'admin123' hashed with default hasher
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'created' => '2023-01-02 00:00:00',
            'modified' => '2023-01-02 00:00:00',
        ],
    ];
}
