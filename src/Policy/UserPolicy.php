<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\User;
use Authorization\IdentityInterface;

/**
 * Users policy
 */
class UserPolicy
{
    /**
     * Check if $user can create Users
     *
     * @param IdentityInterface $user The user.
     * @param User $users
     * @return bool
     */
    public function canManage(IdentityInterface $user, User $users)
    {
        return $user->hasRole('admin');
    }
}
