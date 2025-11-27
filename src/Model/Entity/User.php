<?php
declare(strict_types=1);

namespace Brammo\Auth\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property string|null $email
 * @property string|null $password
 * @property string|null $name
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 */
class User extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'email' => true,
        'password' => true,
        'name' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array<array-key,string>
     */
    protected array $_hidden = [
        'password',
    ];

    /**
     * Hashes the password before saving the user entity.
     *
     * @param string $password The plain text password.
     * @return string|null The hashed password or null if the input is empty.
     */
    protected function _setPassword(string $password): ?string
    {
        if (!empty($password)) {
            return (new DefaultPasswordHasher())->hash($password);
        }

        return null;
    }
}
