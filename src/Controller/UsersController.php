<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\UsersTable;
use Authentication\Controller\Component\AuthenticationComponent;
use Authorization\Controller\Component\AuthorizationComponent;
use Cake\I18n\Time;
use Cake\Mailer\MailerAwareTrait;
use Cake\Utility\Security;

/**
 * Users Controller
 *
 * @property AuthenticationComponent|null Authentication
 * @property UsersTable|null Users
 * @property AuthorizationComponent|null Authorization
 */
class UsersController extends AppController
{
    use MailerAwareTrait;

    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['login', 'logout', 'register', 'verify']);
    }

    public function login()
    {
        $user = $this->Users->newEmptyEntity();
        $this->Authorization->skipAuthorization();
        $result = $this->Authentication->getResult();
        // If the user is logged in send them away.
        if ($result->isValid()) {
            $target = $this->Authentication->getLoginRedirect() ?? '/stundenplan';
            return $this->redirect($target);
        }
        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error('Invalid username or password');
        }
        $this->set(compact('user'));
    }

    public function logout()
    {
        $this->Authentication->logout();
        $this->Authorization->skipAuthorization();
        return $this->redirect(['controller' => 'Pages', 'action' => 'display']);
    }

    public function register()
    {
        $this->Authorization->skipAuthorization();
        $user = $this->Users->newEmptyEntity();

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            $this->Users->patchEntity($user, $data);
            if ($this->Users->save($user)) {
                $this->Flash->success('Dein Account wurde erstellt. Bitte verifiziere deine E-mail Adresse um dich einzuloggen!');
                $this->getMailer('User')->send('register', [$user]);
                return $this->redirect(['controller' => 'users', 'action' => 'login']);
            }
        }

        $this->set(compact('user'));
    }

    public function verify($id = null, $hash = null)
    {
        $this->Authorization->skipAuthorization();

        $user = $this->Users->get($id);
        $checkHash = Security::hash($user->id . $user->created);

        if ($checkHash === $hash) {
            $user->enabled = new Time();
            if ($this->Users->save($user)) {
                $this->Flash->success('Dein Account wurde freigeschalten!');
            } else {
                $this->Flash->error('Es gab einen Fehler beim speichern!');
            }
        } else {
            $this->Flash->error('Der Hash ist nicht korrekt');
        }
        return $this->redirect(['controller' => 'users', 'action' => 'login']);

    }

}
