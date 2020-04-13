<?php


namespace App\View\Helper;


use Authentication\Identity;
use Cake\View\Helper;

class NavHelper extends Helper
{
    public $name = 'Nav';

    public $helpers = ['Html', 'Url'];

    private $navItems = array(
        [
            'title' => 'Startseite',
            'url' => ['controller' => 'pages', 'action' => 'display', 'home'],
            'icon' => 'home'
        ],
        [
            'title' => 'Stundenplan',
            'url' => ['controller' => 'stundenplan', 'action' => 'index'],
            'icon' => 'calendar_today'
        ],
    );

    /**
     * @param Identity|null $loggedIn
     * @return string
     */
    public function render($loggedIn = null)
    {
        if ($loggedIn !== null) {
            $array = [];
            if ($loggedIn->get('role_id') == 3) {
                $array[] = [
                    'title' => 'Benutzerverwaltung',
                    'url' => ['controller' => 'users', 'action' => 'manage'],
                    'icon' => 'account_circle',
                ];
            }
            $array[] =
                [
                    'title' => 'Logout',
                    'url' => ['controller' => 'users', 'action' => 'logout'],
                    'icon' => 'person_outline'
                ];
        } else {
            $array = [
                [
                    'title' => 'Login',
                    'url' => ['controller' => 'users', 'action' => 'login'],
                    'icon' => 'person'
                ]
            ];
        }
        $this->navItems = array_merge($this->navItems, $array);
        if ($this->getView()->getRequest()->is('mobile')) {
            return '<nav class="bot-nav">' . $this->navMobile($this->navItems) . '</nav>';
        }
        return '<nav class="top-nav"><div class="top-nav-title"><a href="#!" class="brand-logo right">Uni<span>Infos</span></a></div><div class="top-nav-links">' . $this->nav($this->navItems) . '</div></nav>';
    }

    private function navMobile(array $items)
    {
        $content = '';

        foreach ($items as $item) {
            $class = array();

            if ($this->isActive($item)) {
                $class[] = 'active';
            }

            $url = $this->getUrl($item);

            $content .= $this->Html->link('<i class="material-icons" style="padding: 20px ' . (100 / count($items) / 3) . 'vw;">' . $item['icon'] . '</i>', $url, [
                'escape' => false,
                'class' => implode(' ', $class),
                'title' => $item['title'],
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
}
