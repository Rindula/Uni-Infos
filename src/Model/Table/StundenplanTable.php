<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Stundenplan;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Stundenplan Model
 *
 * @method Stundenplan get($primaryKey, $options = [])
 * @method Stundenplan newEntity($data = null, array $options = [])
 * @method Stundenplan[] newEntities(array $data, array $options = [])
 * @method Stundenplan|false save(EntityInterface $entity, $options = [])
 * @method Stundenplan saveOrFail(EntityInterface $entity, $options = [])
 * @method Stundenplan patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Stundenplan[] patchEntities($entities, array $data, array $options = [])
 * @method Stundenplan findOrCreate($search, callable $callback = null, $options = [])
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
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('uid')
            ->maxLength('uid', 255)
            ->requirePresence('uid', 'create')
            ->notEmptyString('uid');

        $validator
            ->scalar('note')
            ->allowEmptyString('note');

        $validator
            ->scalar('loggedInNote')
            ->allowEmptyString('loggedInNote');

        $validator
            ->scalar('info_for_db')
            ->maxLength('info_for_db', 255)
            ->requirePresence('info_for_db', 'create')
            ->notEmptyString('info_for_db');

        return $validator;
    }

    /**
     * @param string $uid
     * @param string $type
     * @return Query
     */
    public function findByUid($uid, $type = 'all')
    {
        return $this->find($type)->where([
            'uid IS' => $uid
        ]);
    }
}
