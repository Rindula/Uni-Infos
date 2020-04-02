<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Authentication\IdentityInterface;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * User Entity
 *
 * @property int $id
 * @property int $role_id
 * @property Role $role
 * @property string $email
 * @property string $password
 * @property FrozenTime $created
 * @property FrozenTime $modified
 * @property FrozenTime|null $enabled
 */
class User extends Entity implements IdentityInterface
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'role_id' => true,
        'role' => true,
        'email' => true,
        'password' => true,
        'created' => true,
        'modified' => true,
        'enabled' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password',
    ];

    protected function _setPassword(string $password): ?string
    {
        if (strlen($password) > 0) {
            return (new DefaultPasswordHasher())->hash($password);
        }
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $entity = $usersTable->get($this->id, ['contain' => ['Roles']]);
        return $entity->role->alias == $role;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getOriginalData()
    {
        return $this;
    }
}
