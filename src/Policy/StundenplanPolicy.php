<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Stundenplan;
use Authorization\IdentityInterface;

/**
 * Stundenplan policy
 */
class StundenplanPolicy
{
    /**
     * Check if $user can create Stundenplan
     *
     * @param IdentityInterface $user The user.
     * @param Stundenplan $stundenplan
     * @return bool
     */
    public function canCreate(IdentityInterface $user, Stundenplan $stundenplan)
    {
        return false;
    }

    /**
     * Check if $user can update Stundenplan
     *
     * @param IdentityInterface $user The user.
     * @param Stundenplan $stundenplan
     * @return bool
     */
    public function canUpdate(IdentityInterface $user, Stundenplan $stundenplan)
    {
        return $user->hasRole('admin');
    }

    /**
     * Check if $user can delete Stundenplan
     *
     * @param IdentityInterface $user The user.
     * @param Stundenplan $stundenplan
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Stundenplan $stundenplan)
    {
        return $user->hasRole('admin');
    }

    /**
     * Check if $user can view Stundenplan
     *
     * @param IdentityInterface $user The user.
     * @param Stundenplan $stundenplan
     * @return bool
     */
    public function canView(IdentityInterface $user, Stundenplan $stundenplan)
    {
        return false;
    }
}
