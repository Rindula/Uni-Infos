<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @method User newEmptyEntity()
 * @method User newEntity(array $data, array $options = [])
 * @method User[] newEntities(array $data, array $options = [])
 * @method User get($primaryKey, $options = [])
 * @method User findOrCreate($search, ?callable $callback = null, $options = [])
 * @method User patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method User[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method User|false save(EntityInterface $entity, $options = [])
 * @method User saveOrFail(EntityInterface $entity, $options = [])
 * @method User[]|ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method User[]|ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method User[]|ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method User[]|ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin TimestampBehavior
 */
class UsersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        $validator
            ->dateTime('enabled')
            ->allowEmptyDateTime('enabled');

        $validator
            ->scalar('course')
            ->allowEmptyString('course');

        return $validator;
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationPassword(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('id')
            ->notEmptyString('id');

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password')
            ->notEmptyString('password', __('The Password must not be empty!'));

        $validator
            ->scalar('passwordConfirm')
            ->maxLength('passwordConfirm', 255)
            ->requirePresence('passwordConfirm')
            ->notEmptyString('passwordConfirm')
            ->equalToField('passwordConfirm', 'password', __('The passwords must match!'));

        return $validator;
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationCourse(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('id')
            ->notEmptyString('id');

        $validator
            ->scalar('course')
            ->maxLength('course', 10)
            ->requirePresence('course')
            ->notEmptyString('course', __('The Course must not be empty!'))
            ->inList('course', getCourses(false, true));

        return $validator;
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationLanguage(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('id')
            ->notEmptyString('id');

        $validator
            ->scalar('language')
            ->maxLength('language', 10)
            ->requirePresence('language')
            ->notEmptyString('language', __('The Language must not be empty!'))
            ->inList('course', ['de', 'en', 'es'], 'The selected language is not available!');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param RulesChecker $rules The rules object to be modified.
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['role_id'], 'Roles'));

        return $rules;
    }
}
