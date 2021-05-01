<?php
declare(strict_types=1);

use Authentication\Controller\Component\AuthenticationComponent;

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 * @property AuthenticationComponent Authentication
 * @var boolean $loggedIn Ist der Benutzer eingeloggt, oder nicht?
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\I18n\I18n;
use Exception;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     * @throws Exception
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('Authorization.Authorization');
        $allowed_langs = array ('es', 'en', 'de');

        $lang = lang_getfrombrowser ($allowed_langs, 'en', null, false);

        I18n::setLocale($lang ?? 'en');
        $loggedIn = $this->Authentication->getIdentity();
        $this->set(compact('loggedIn'));
        if ($loggedIn) I18n::setLocale($this->getTableLocator()->get('users')->get($this->Authentication->getIdentity()->id)->language ?? 'en');
        unset($loggedIn);

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
    }

}
