<?php
/**
 * @var \App\View\AppView $this
 * @var array $events
 */

echo $this->Html->css("progressbar.css");
$this->Html->script("pages/stundenplan.js", ['block' => 'bottomScripts']);
?>
<?= $this->Form->select('course', $courses, ['id' => 'courseSelector', 'default' => $courseSelected]) ?>
<div id="list">
</div>
