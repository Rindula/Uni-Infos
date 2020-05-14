<?php
/**
 * @var AjaxView $this
 * @var Stundenplan $stundenplan
 */

use App\Model\Entity\Stundenplan;
use App\View\AjaxView;

?>

<?= $this->Form->create($stundenplan) ?>
<?= $this->Form->controls([
    'note' => ['label' => 'Notiz'],
    'loggedInNote' => ['label' => 'Notiz für eingeloggte Nutzer'],
    'isOnline' => ['label' => 'Findet Online statt?'],
], ['legend' => 'Eintrag bearbeiten']) ?>
<?= $this->Form->submit() ?>
<?= $this->Form->end() ?>
