<?php
/**
 * @var AppView $this
 * @var array $events
 */

$this->Html->script("pages/stundenplan.js?" . $this->Git->getTimestamp()->timestamp, ['block' => 'bottomScripts']);

use App\View\AppView; ?>
<?php if ($loggedIn): ?>
    <script>
        var loggedIn = true;
    </script>
<?php endif; ?>
<div class="content">
    <?= $this->Form->select('course', $courses, ['id' => 'courseSelector', 'default' => $courseSelected, 'empty' => __('Please select your course')]) ?>
    <?= $this->Form->control('onlineOnly', ['type' => 'toggleButton', 'id' => 'onlineOnly', 'label' => __('Online Only')]) ?>
    <div id="list">
    </div>
</div>
<template id="lessonTemplate">
    <blockquote>
        <div class="row row-top">
            <span data-role="title" class="column column-20">TITLE</span>
            <span data-role="location" class="column-offset-50 column-30 column"
                  style='text-align: right'>LOCATION</span>
        </div>
        <div class='row'>
            <small data-role="description" class='column'>DESCRIPTION</small>
        </div>
        <br>
        <div data-role="notes" class='message'>NOTES</div>
        <div data-role="loggedinnotes" class='message'>LOGGEDINNOTES</div>
        <a data-role="editnotes" class='' href='#'>📝</a><br>
        <div data-role="deletenotes">
            <?= $this->Html->link('Notiz löschen', ['controller' => 'stundenplan', 'action' => 'delete', '__UID__', 'note'], ['confirm' => 'Bist du sicher, dass du die Notiz löschen willst?']) . "<br>" . $this->Html->link('Eingeloggten Notiz löschen', ['controller' => 'stundenplan', 'action' => 'delete', '__UID__', 'loggedInNote'], ['confirm' => 'Bist du sicher, dass du die Eingeloggten Notiz löschen willst?']) . "<br>" . $this->Html->link('Alle Notizen löschen', ['controller' => 'stundenplan', 'action' => 'delete', '__UID__', 'all'], ['confirm' => 'Bist du sicher, dass du die alle Notizen dieser Stunde löschen willst?']) ?>
        </div>
        <div class='row mobile-margin-down'>
            <div class='column column-20'>Beginn:</div>
            <div data-role="beginntime" class='column column-80'>BEGINNTIME</div>
        </div>
        <div class='row'>
            <div class='column column-20'>Ende:</div>
            <div data-role="endtime" class='column column-80'>ENDTIME</div>
        </div>
        <br>
        <div data-role="progress-wrapper" class='column'>
            <div class="progress">
                <div data-role="progressbar" class="progress-value" data-percent='' style="width: 0">0%</div>
            </div>
        </div>
    </blockquote>
</template>
