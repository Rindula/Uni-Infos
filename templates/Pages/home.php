<?php
/**
 * @var \App\View\AppView $this
 */
?>
<h1 style="width: 100%; text-align: center">UniInfos</h1>
<h2 style="width: 100%; text-align: center">Links</h2>
<div style="text-align: center">
<?= $this->Html->link("Zum Stundenplan", ['controller' => 'stundenplan', 'action' => 'index'], ['class' => 'button button-clear']) ?>
<?= $this->Html->link("Zum Dateispeicher", 'https://files.rindula.de/nextcloud/index.php/s/Fj3YTt9Mcga287L', ['class' => 'button button-outline', 'target' => '_blank']) ?>
</div>
