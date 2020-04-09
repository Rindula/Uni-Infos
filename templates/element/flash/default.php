<?php
/**
 * @var AppView $this
 * @var array $params
 * @var string $message
 */
$class = 'message';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}

use App\View\AppView; ?>
<div class="<?= h($class) ?>"><?= $message ?><span class="float-right" style="cursor: pointer"
                                                   onclick="$(this).parent().fadeOut()">x</span></div>
