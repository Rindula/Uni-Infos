<?php
/**
 * @var AppView $this
 * @var array $events
 * @var array $translations
 */

$this->Html->script("pages/stundenplan.js?" . $this->Git->getTimestamp()->timestamp, ['block' => 'bottomScripts']);

use App\View\AppView; ?>
    <script>
        <?php if ($loggedIn): ?>
        var loggedIn = true;
        <?php endif; ?>
        var translations = <?= json_encode($translations) ?>
    </script>
<div class="content">
    <?= $this->Form->select('course', $courses, ['id' => 'courseSelector', 'default' => $courseSelected, 'empty' => __('Please select your course'), ]) ?>
    <?= $this->Form->control('onlineOnly', ['type' => 'toggleButton', 'id' => 'onlineOnly', 'label' => __('Online Only')]) ?>
    <div id="list">
    </div>
</div>
<template id="lessonTemplate">
    <blockquote>
        <div class="row row-top">
            <span data-role="title" class="column column-20"></span>
            <span data-role="location" class="column-offset-50 column-30 column"
                  style='text-align: right'></span>
        </div>
        <div class='row'>
            <small data-role="description" class='column'></small>
        </div>
        <br>
        <div data-role="notes" class='message'></div>
        <div data-role="loggedinnotes" class='message'></div>
        <a data-role="editnotes" class='' href='#'>📝</a><br>
        <div data-role="deletenotes">
            <?= $this->Html->link(__('Delete note'), ['controller' => 'stundenplan', 'action' => 'delete', '__UID__', 'note'], ['confirm' => __('Are you sure you want to remove this note?')]) . "<br>" . $this->Html->link(__('Delete logged in notes'), ['controller' => 'stundenplan', 'action' => 'delete', '__UID__', 'loggedInNote'], ['confirm' => __('Are you sure that you want to delete the logged in note?')]) . "<br>" . $this->Html->link(__('Delete all notes'), ['controller' => 'stundenplan', 'action' => 'delete', '__UID__', 'all'], ['confirm' => __('Are you sure you want to delete all notes of this lesson?')]) ?>
        </div>
        <div class='row mobile-margin-down'>
            <div class='column column-20'><?=__("Begin")?>:</div>
            <div data-role="beginntime" class='column column-80'></div>
        </div>
        <div class='row'>
            <div class='column column-20'><?= __("End") ?>:</div>
            <div data-role="endtime" class='column column-80'></div>
        </div>
        <br>
        <div data-role="progress-wrapper" class='column'>
            <div class="progress">
                <div data-role="progressbar" class="progress-value" data-percent='' style="width: 0">0%</div>
            </div>
        </div>
    </blockquote>
</template>
