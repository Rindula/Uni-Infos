<?php
/**
 * @var \App\View\AppView $this
 * @var array $events
 */

echo $this->Html->css("progressbar.css");
$this->Html->script("pages/stundenplan.js", ['block' => 'bottomScripts']);
?>
<?php if ($loggedIn): ?>
    <script>
        var loggedIn = true;
    </script>
<?php endif; ?>
<?= $this->Form->select('course', $courses, ['id' => 'courseSelector', 'default' => $courseSelected, 'empty' => 'Bitte Kurs auswählen']) ?>
<div id="list">
</div>
