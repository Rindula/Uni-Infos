<?php
/**
 * @var AppView $this
 * @var array $params
 * @var string $message
 */
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}

use App\View\AppView; ?>
<div class="message success"><?= $message ?><span class="float-right" style="cursor: pointer"
                                                  onclick="$(this).parent().fadeOut()">x</span></div>
