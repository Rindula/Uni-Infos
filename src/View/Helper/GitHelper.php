<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\View\Helper;
use Cake\View\Helper\HtmlHelper;

/**
 * Git helper
 * @property HtmlHelper Html
 */
class GitHelper extends Helper
{

    protected $helpers = [
        'Html',
    ];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];
    /** @var Time */
    private $timestamp;
    /** @var string */
    private $hash;
    /** @var string */
    private $message;
    /** @var string */
    private $shorthash;

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $result = !Configure::read('debug') ? shell_exec('cd /var/www/vhosts/rindula.de/git/interface.git && git log -1 --pretty=format:\'%h~#~%H~#~%s~#~%ci\' --abbrev-commit') : str_replace('\'', '', shell_exec('cd ' . ROOT . " && git log -1 --pretty=format:'%h~#~%H~#~%s~#~%ci' --abbrev-commit"));
        list($this->shorthash, $this->hash, $this->message, $timestamp) = explode('~#~', $result);
        $this->timestamp = new Time($timestamp);
    }

    public function getFooterInfos()
    {
        $prestring = '';
        if (Configure::read('debug')) $prestring = 'DEVELOPMENT EDITION - ';
        return $prestring . $this->Html->link($this->shorthash, 'https://gitlab.com/Rindula/interface/-/commit/' . $this->hash, ['target' => '_blank', 'rel' => 'noopener']) . ' - ' . $this->message . ' (' . $this->timestamp->nice() . ')';
    }
}
