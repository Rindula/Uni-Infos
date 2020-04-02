<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\User;
use Authorization\IdentityInterface;

/**
 * user policy
 */
class userPolicy
{
    /**
     * Check if $user can logout
     *
     * @param IdentityInterface $user The user.
     * @param User $resource
     * @return bool
     */
    public function canLogout(IdentityInterface $user, User $resource)
    {
        return true;
    }
}
