<?php
/**
 * @var AppView $this
 * @var User $user
 * @var mixed $courses
 */

use App\Model\Entity\User;
use App\View\AppView;

?>
<div class="content">
    <h3><?= __('Preferences') ?></h3>
    <div id="stats">
        <table class="table-responsive">
            <tr>
                <td>E-Mail</td>
                <td><?= $user->email ?> <span style="color: orangered;cursor: help"
                                              title="Wenn du eine andere E-mail nutzen möchtest, erstelle dir bitte einen neuen Account und deaktiviere diesen!">?</span>
                </td>
            </tr>
            <tr>
                <td>Aktiviert seit:</td>
                <td><?= $user->enabled->nice() ?></td>
            </tr>
        </table>
    </div>
    <hr>
    <div id="settings">
        <h4><?= __('Reset Password') ?></h4>
        <?= $this->Form->create($user) ?>
        <?= $this->Form->password('password', ['value' => '', 'placeholder' => 'Passwort']) ?>
        <?= $this->Form->password('passwordConfirm', ['value' => '', 'placeholder' => 'Passwort bestätigen']) ?>
        <?= $this->Form->submit(__('Change Password')) ?>
        <?= $this->Form->end() ?>
        <h4><?= __('Set Course') ?></h4>
        <?= $this->Form->create($user) ?>
        <?= $this->Form->control('course', ['options' => $courses, 'empty' => 'Bitte Kurs auswählen']) ?>
        <?= $this->Form->submit(__('Set Course')) ?>
        <?= $this->Form->end() ?>
        <h4><?= __('Disable Account') ?></h4>
        <?= $this->Form->postButton(__('Disable Account'), ['controller' => 'users', 'action' => 'disable'], ['class' => 'button-outline delete', 'confirm' => __('Are you sure you want to disable this Account?')]) ?>
    </div>
</div>
