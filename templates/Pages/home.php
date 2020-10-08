<?php
/**
 * @var AppView $this
 */

use App\View\AppView; ?>
<h1 style="width: 100%; text-align: center">UniInfos</h1>
<h2 style="width: 100%; text-align: center">Links</h2>
<div style="text-align: center">
    <?= $this->Html->link(__("To file storage"), 'https://files.rindula.de/nextcloud/index.php/s/FffjYnKGNjYiNMb', ['class' => 'button button-outline', 'target' => '_blank']) ?>
    <?= $this->Html->link(__("To the timetable"), ['controller' => 'stundenplan', 'action' => 'index'], ['class' => 'button button-clear']) ?>
    <!--    <br>-->
    <?= ""//$this->Html->link("<img style='width: 200px' alt='Jetzt bei Google Play' src='https://play.google.com/intl/en_us/badges/static/images/badges/de_badge_web_generic.png'/>", 'https://play.google.com/store/apps/details?id=de.rindula.uniinfos&pcampaignid=pcampaignidMKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1', ['target' => '_blank', 'escape' => false])  ?>
</div>
