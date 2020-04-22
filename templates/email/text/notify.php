<?php
/**
 * @var AppView $this
 * @var User $user
 */

use App\Model\Entity\User;
use App\View\AppView;

?>
Hallo,

es gibt einen neuen Benutzer: <?= $this->Text->autoLinkEmails($user->email) . PHP_EOL ?>

Mit freundlichen Grüßen
