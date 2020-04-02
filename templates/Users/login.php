<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>

<?= $this->Form->create($user) ?>
<?=
$this->Form->controls([
    'email' => [],
    'password' => [],
])
?>
<?= $this->Form->submit('Login') ?>
<?= $this->Form->end() ?>
