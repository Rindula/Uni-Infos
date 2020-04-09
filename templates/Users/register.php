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
    'password_confirm' => [],
], ['legend' => 'Registrieren'])
?>
<?= $this->Form->submit('Registrieren') ?>
<?= $this->Form->end() ?>
