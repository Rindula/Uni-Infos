<?php
declare(strict_types=1);

namespace App\Mailer;

use Cake\Mailer\Mailer;
use Cake\Utility\Security;

/**
 * User mailer.
 */
class UserMailer extends Mailer
{
    /**
     * Mailer's name.
     *
     * @var string
     */
    public static $name = 'User';

    public function register($user)
    {
        $hash = Security::hash($user->id . $user->created);
        $link = 'https://' . env('SERVER_NAME') . '/users/verify/' . $user->id . '/' . $hash;

        $this
            ->setTransport('default')
            ->viewBuilder()
            ->setTemplate('register')
            ->setLayout('default');
        $this
            ->setEmailFormat('both')
            ->setTo($user->email)
            ->setSubject(__('E-Mail Verifizierung | rindula.de'))
            ->setViewVars(['link' => $link, 'user' => $user]);
    }
}
