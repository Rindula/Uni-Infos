<?php
/**
 * @var AppView $this
 * @var CalendarConfiguratorForm $calendarConfigurator
 * @var string $link
 */

use App\Form\CalendarConfiguratorForm;
use App\View\AppView;

if (!empty($link)) {
    echo $this->Form->text('link', ['readonly' => true, 'value' => $link, 'onclick' => 'this.focus(); this.select()']);
}
echo $this->Form->create($calendarConfigurator);
echo $this->Form->allControls([], ['legend' => false]);
echo $this->Form->submit();
echo $this->Form->end();
