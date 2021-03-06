<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var AppView $this
 * @var Identity|null $loggedIn Ist der Benutzer eingeloggt, oder nicht?
 */

$cakeDescription = 'UniInfos';

use App\View\AppView;
use Authentication\Identity;
use Cake\I18n\I18n;

?>
<!DOCTYPE html>
<html lang="<?= I18n::getLocale() ?>">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $cakeDescription ?> |
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>
    <meta name="google-site-verification" content="Je2chFzpwnsYGfFP1WVL_-uM0rY4SDWX50MTL5ZviuY"/>

    <link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.1/normalize.css">

    <?= $this->Html->css('https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.css') ?>
    <?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
    <?= $this->Html->css('main.css?' . $this->Git->getTimestamp()->timestamp) ?>
    <?= $this->Html->script('jquery.min.js') ?>
    <?= $this->Html->script('https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.js', ['block' => 'bottomScripts']) ?>
    <?= ($this->getRequest()->is('mobile')) ? $this->Html->script('cookieConsentRight.js', ['block' => 'bottomScripts']) : $this->Html->script('cookieConsentBottom.js', ['block' => 'bottomScripts']) ?>
    <?= $this->Html->script('main.min.js?' . $this->Git->getTimestamp()->timestamp) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>


    <?php if (isset($_COOKIE["cookieconsent_status"]) && $_COOKIE["cookieconsent_status"] !== 'deny'): ?>
        <!-- Google Tag Manager -->
        <script>(function (w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start':
                        new Date().getTime(), event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', 'GTM-TKCPTMH');</script>
        <!-- End Google Tag Manager -->

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-158268171-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());

            gtag('config', 'UA-158268171-1', {
                'storage': 'none'
            });
        </script>
    <?php endif; ?>
</head>
<body>
<?php if (isset($_COOKIE["cookieconsent_status"]) && $_COOKIE["cookieconsent_status"] !== 'deny'): ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TKCPTMH"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->
<?php endif; ?>
<?= ($this->getRequest()->is('mobile')) ? '' : $this->Nav->render($loggedIn) ?>
<main class="main">
    <div id="scrollbar"></div>
    <div id="scrollPath"></div>
    <div class="container">
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </div>
</main>
<?= ($this->getRequest()->is('mobile')) ? $this->Nav->render($loggedIn) : $this->Html->tag('footer', $this->Git->getFooterInfos() . '<br>' . '&copy; ' . date('Y') . ' rindula.de --- ' . $this->Html->link("Impressum", ['controller' => 'datenschutz', 'action' => 'impressum']) . ' | ' . $this->Html->link("Datenschutzerklärung", ['controller' => 'datenschutz', 'action' => 'index']), ['id' => 'footer']) ?>
<?= $this->fetch('bottomScripts') ?>
<script>
    // Scrollbar
    let scrollprogress = document.getElementById('scrollbar');
    let totalHeight = document.body.scrollHeight - window.innerHeight;

    window.onscroll = function () {
        let scrollHeight = (window.pageYOffset / totalHeight) * 100;
        scrollprogress.style.height = scrollHeight + "%";
    }
</script>
</body>
</html>
