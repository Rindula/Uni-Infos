<?php
/**
 * @var AppView $this
 * @var User $user
 */

use App\Model\Entity\User;
use App\View\AppView;

?>

<?= $this->Form->create($user) ?>
<?=
$this->Form->controls([
    'email' => [],
    'password' => [],
], ['legend' => 'Login'])
?>
<?= $this->Form->submit('Login') ?>
<?= $this->Html->link('Kein Account? Jetzt registrieren', ['controller' => 'users', 'action' => 'register']) ?>
<?= $this->Form->end() ?>
