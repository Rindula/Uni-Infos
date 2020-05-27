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
    <?= $this->Form->select('course', $courses, ['id' => 'courseSelector', 'default' => $courseSelected, 'empty' => 'Bitte Kurs auswählen']) ?>
    <?= $this->Form->control('onlineOnly', ['type' => 'toggleButton', 'id' => 'onlineOnly']) ?>
    <div id="list">
    </div>
</div>
