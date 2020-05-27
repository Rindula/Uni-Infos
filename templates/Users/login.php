<?php
/**
 * @var AppView $this
 * @var User $user
 */

use App\Model\Entity\User;
use App\View\AppView;

?>
<div class="content">
    <?= $this->Form->create($user) ?>
    <?=
    $this->Form->controls([
        'email' => [],
        'password' => [],
    ], ['legend' => 'Login'])
    ?>
    <?= $this->Form->submit('Login') ?>
    <?= $this->Html->link(__('No account yet? Register now'), ['controller' => 'users', 'action' => 'register']) ?>
    <?= $this->Form->end() ?>
</div>
