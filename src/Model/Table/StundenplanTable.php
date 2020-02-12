<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Stundenplan Model
 *
 * @method \App\Model\Entity\Stundenplan get($primaryKey, $options = [])
 * @method \App\Model\Entity\Stundenplan newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Stundenplan[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Stundenplan|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Stundenplan saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Stundenplan patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Stundenplan[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Stundenplan findOrCreate($search, callable $callback = null, $options = [])
 */
class StundenplanTable extends Table
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

        $this->setTable('stundenplan');
        $this->setDisplayField('uid');
        $this->setPrimaryKey('uid');
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('uid')
            ->maxLength('uid', 255)
            ->requirePresence('uid', 'create')
            ->notEmptyString('uid');

        $validator
            ->scalar('note')
            ->allowEmptyString('note');

        $validator
            ->scalar('info_for_db')
            ->maxLength('info_for_db', 255)
            ->requirePresence('info_for_db', 'create')
            ->notEmptyString('info_for_db');

        return $validator;
    }
}
