<?php
declare(strict_types=1);

namespace Brammo\Auth\Model\Table;

use Brammo\Auth\Model\Entity\User;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @method \Brammo\Auth\Model\Entity\User newEntity(array $data, array $options = [])
 * @method array<\Brammo\Auth\Model\Entity\User> newEntities(array $data, array $options = [])
 * @method \Brammo\Auth\Model\Entity\User get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \Brammo\Auth\Model\Entity\User findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method array<\Brammo\Auth\Model\Entity\User> patchEntities(iterable $entities, array $data, array $options = [])
 * @extends \Cake\ORM\Table<array<string, \Cake\ORM\Behavior>, \Brammo\Auth\Model\Entity\User>
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->notEmptyString('name')
            ->requirePresence('name', 'create');

        $validator
            ->email('email')
            ->maxLength('email', 255)
            ->notEmptyString('email')
            ->requirePresence('email', 'create');

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->allowEmptyString('password');

        $validator
            ->scalar('status')
            ->inList('status', [User::STATUS_ACTIVE, User::STATUS_NEW, User::STATUS_BLOCKED])
            ->notEmptyString('status');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['email']), ['errorField' => 'email']);

        return $rules;
    }

    /**
     * Find active users
     *
     * Filters query to only return users with active status.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query to modify
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findActive(SelectQuery $query): SelectQuery
    {
        return $query->where([$this->aliasField('status') => User::STATUS_ACTIVE]);
    }
}
