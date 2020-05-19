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
            ->setFrom('service@rindula.de', 'rindula.de | Uniseite')
            ->setSubject(__('Email verification | rindula.de'))
            ->setViewVars(['link' => $link, 'user' => $user]);
    }

    public function notify($user)
    {
        $this
            ->setTransport('default')
            ->viewBuilder()
            ->setTemplate('notify')
            ->setLayout('default');
        $this
            ->setEmailFormat('both')
            ->setTo("webmaster@rindula.de")
            ->setFrom('service@rindula.de', 'rindula.de | Uniseite')
            ->setSubject(__('New User | rindula.de'))
            ->setViewVars(['user' => $user]);
    }
}
