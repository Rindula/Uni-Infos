<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Role;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\Association\HasMany;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Roles Model
 *
 * @property UsersTable&HasMany $Users
 *
 * @method Role newEmptyEntity()
 * @method Role newEntity(array $data, array $options = [])
 * @method Role[] newEntities(array $data, array $options = [])
 * @method Role get($primaryKey, $options = [])
 * @method Role findOrCreate($search, ?callable $callback = null, $options = [])
 * @method Role patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Role[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method Role|false save(EntityInterface $entity, $options = [])
 * @method Role saveOrFail(EntityInterface $entity, $options = [])
 * @method Role[]|ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method Role[]|ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method Role[]|ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method Role[]|ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class RolesTable extends Table
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

        $this->setTable('roles');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Users', [
            'foreignKey' => 'role_id',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('alias')
            ->maxLength('alias', 255)
            ->requirePresence('alias', 'create')
            ->notEmptyString('alias')
            ->add('alias', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

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
        $rules->add($rules->isUnique(['alias']));

        return $rules;
    }

    /**
     * @param string $alias
     * @return array|bool|Role|null
     */
    public function findByAlias($alias = "")
    {
        $role = $this->find()->where([
            'alias' => $alias
        ])->first();
        if ($role) return $role;
        return false;
    }
}
