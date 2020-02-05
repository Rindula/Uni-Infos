<?php


namespace App\View\Helper;


use Cake\View\Helper;

class NavHelper extends Helper
{
    public $name = 'Nav';

    public $helpers = ['Html', 'Url'];

    private $navItems = array(
        [
            'title' => 'Startseite',
            'url' => ['controller' => 'pages', 'action' => 'display', 'home']
        ],
//        [
//            'title' => 'Hydra',
//            'url' => ['controller' => 'hydra', 'action' => 'index']
//        ],
//        [
//            'title' => 'NMap',
//            'url' => ['controller' => 'nmap', 'action' => 'index']
//        ],
//        [
//            'title' => 'E-Bon',
//            'url' => ['controller' => 'email', 'action' => 'ebon']
//        ],
        [
            'title' => 'Stundenplan',
            'url' => ['controller' => 'stundenplan', 'action' => 'index']
        ],
//        [
//            'title' => 'Einstellungen',
//            'url' => ['controller' => 'settings', 'action' => 'index']
//        ]
    );

    public function render()
    {
        return '<nav class="top-nav"><div class="top-nav-title"><a href="#!" class="brand-logo right">Uni<span>Infos</span></div></div></a></div><div class="top-nav-links">' . $this->nav($this->navItems) . '</div></nav>';
    }

    private function nav(array $items)
    {
        $content = '';

        foreach ($items as $item) {
            $class = array();

            if ($this->isActive($item)) {
                $class[] = 'active';
            }

            $url = $this->getUrl($item);

            $content .= $this->Html->link($item['title'], $url, [
                    'escape' => false,
                    'class' => implode(' ', $class),
            ]);
        }

        return $content;
    }

    private function isActive($item)
    {
        $url = $this->Url->build($this->getUrl($item));
        if ($this->getView()->getRequest()->getRequestTarget() == $url || ($url != '/' && strlen($this->getView()->getRequest()->getRequestTarget()) > strlen($url) && substr($this->getView()->getRequest()->getRequestTarget(), 0, strlen($url)) == $url)) {
            return true;
        }
        return false;
    }

    private function getUrl($item)
    {
        $url = false;
        if (!empty($item['url'])) {
            $url = $item['url'];
        }

        return $url;
    }
}
