<?php
/**
 * @var AppView $this
 * @var User[]|CollectionInterface $users
 */

use App\Model\Entity\User;
use App\View\AppView;
use Cake\Collection\CollectionInterface;

?>
<div class="users index content">
    <h3><?= __('Users') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('role_id') ?></th>
                <th><?= $this->Paginator->sort('email') ?></th>
                <th><?= $this->Paginator->sort('enabled') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <?= $this->Form->create($user); ?>
                <?= $this->Form->hidden('user.id', ['value' => $user->id]); ?>
                <?= $this->Html->tableCells([
                    $this->Number->format($user->id),
                    ($this->Identity->getId() === $user->id) ? $user->role->name : $this->Form->control('user.role_id', ['label' => false, 'options' => $options, 'value' => $user->role_id]),
                    h($user->email),
                    h($user->enabled) ?? '---',
                    (($user->enabled) ? '' : $this->Html->link(__('Send activation email'), ['action' => 'userAction', $user->id, 'sendmail'])) . $this->Form->submit('Benutzer speichern')
                ]) ?>
                <?= $this->Form->end(); ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
