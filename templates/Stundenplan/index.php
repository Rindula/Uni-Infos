<?php
/**
 * @var AppView $this
 * @var array $events
 */

$this->Html->script("pages/stundenplan.js", ['block' => 'bottomScripts']);

use App\View\AppView; ?>
<?php if ($loggedIn): ?>
    <script>
        var loggedIn = true;
    </script>
<?php endif; ?>
<?= $this->Form->select('course', $courses, ['id' => 'courseSelector', 'default' => $courseSelected, 'empty' => 'Bitte Kurs auswählen']) ?>
<div id="list">
</div>
