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
        if (Configure::read('unittest_running', false)) {
            $this->shorthash = $this->hash = $this->message = "HASH COMES HERE";
            $timestamp = time();
        } else {
            parent::initialize($config);
            $result = !Configure::read('debug') ? shell_exec('cd /var/www/vhosts/rindula.de/git/Uni-Infos.git && git log -1 --pretty=format:\'%h~#~%H~#~%s~#~%ci\' --abbrev-commit') : str_replace('\'', '', shell_exec('cd ' . ROOT . " && git log -1 --pretty=format:'%h~#~%H~#~%s~#~%ci' --abbrev-commit"));
            list($this->shorthash, $this->hash, $this->message, $timestamp) = explode('~#~', $result);
        }
        $this->timestamp = new Time($timestamp, "UTC");
    }

    public function getFooterInfos()
    {
        $prestring = '<img style="height: 14px" src="https://github.com/Rindula/Uni-Infos/actions/workflows/ci.yml/badge.svg"> ';
        if (Configure::read('debug') || Configure::read('unittest_running', false)) $prestring = 'DEVELOPMENT EDITION - ';
        return $prestring . $this->Html->link($this->shorthash, 'https://github.com/Rindula/Uni-Infos/commit/' . $this->hash, ['target' => '_blank', 'rel' => 'noopener']) . ' - ' . $this->message . ' (' . $this->timestamp->nice() . ')';
    }

    /**
     * @return Time
     */
    public function getTimestamp(): Time
    {
        return $this->timestamp;
    }
}
