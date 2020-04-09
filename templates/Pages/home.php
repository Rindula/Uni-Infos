<?php
/**
 * @var AppView $this
 */

use App\View\AppView; ?>
<h1 style="width: 100%; text-align: center">UniInfos</h1>
<h2 style="width: 100%; text-align: center">Links</h2>
<div style="text-align: center">
    <?= $this->Html->link("Zum Dateispeicher", 'https://files.rindula.de/nextcloud/index.php/s/GEEgyjNm6zS7xE9', ['class' => 'button button-outline', 'target' => '_blank']) ?>
    <?= $this->Html->link("Zum Stundenplan", ['controller' => 'stundenplan', 'action' => 'index'], ['class' => 'button button-clear']) ?>
    <?= $this->Html->link("Sitzungsaufzeichnugnen", 'https://files.rindula.de/nextcloud/index.php/s/z4mi9YMsr74s3AQ', ['class' => 'button button-outline', 'target' => '_blank']) ?>
    <br>
    <?= $this->Html->link("<img style='width: 200px' alt='Jetzt bei Google Play' src='https://play.google.com/intl/en_us/badges/static/images/badges/de_badge_web_generic.png'/>", 'https://play.google.com/store/apps/details?id=de.rindula.uniinfos&pcampaignid=pcampaignidMKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1', ['target' => '_blank', 'escape' => false]) ?>
</div>
