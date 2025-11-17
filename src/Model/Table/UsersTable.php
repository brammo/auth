<?php
declare(strict_types=1);

namespace Brammo\Auth\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @method \Auth\Model\Entity\User newEmptyEntity()
 * @method \Auth\Model\Entity\User newEntity(array $data, array $options = [])
 * @method array<\Auth\Model\Entity\User> newEntities(array $data, array $options = [])
 * @method \Auth\Model\Entity\User get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \Auth\Model\Entity\User findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \Auth\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\Auth\Model\Entity\User> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \Auth\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \Auth\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\Auth\Model\Entity\User>|\Cake\Datasource\ResultSetInterface<\Auth\Model\Entity\User>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\Auth\Model\Entity\User>|\Cake\Datasource\ResultSetInterface<\Auth\Model\Entity\User> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\Auth\Model\Entity\User>|\Cake\Datasource\ResultSetInterface<\Auth\Model\Entity\User>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\Auth\Model\Entity\User>|\Cake\Datasource\ResultSetInterface<\Auth\Model\Entity\User> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @extends \Cake\ORM\Table<\Brammo\Auth\Model\Entity\User>
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
            ->notEmptyString('name');

        $validator
            ->email('email')
            ->maxLength('email', 255)
            ->notEmptyString('email');

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->allowEmptyString('password');

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
}
