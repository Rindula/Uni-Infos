<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Stundenplan Entity
 *
 * @property string $uid
 * @property string|null $note
 * @property string|null $loggedInNote
 * @property string $info_for_db
 */
class Stundenplan extends Entity
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
        'uid' => true,
        'note' => true,
        'loggedInNote' => true,
        'info_for_db' => true,
    ];
}
