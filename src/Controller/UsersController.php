<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\UsersTable;
use Authentication\Controller\Component\AuthenticationComponent;
use Authorization\Controller\Component\AuthorizationComponent;
use Cake\Http\Exception\BadRequestException;
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
        $this->loadComponent('Paginator');
    }

    public function login()
    {
        $user = $this->Users->newEmptyEntity();
        $this->Authorization->skipAuthorization();
        $result = $this->Authentication->getResult();
        // If the user is logged in send them away.
        if ($result->isValid()) {
            if (!empty($result->getData()->enabled) && (new Time())->diffInSeconds($result->getData()->enabled) > 0) {
                $target = $this->Authentication->getLoginRedirect() ?? '/';
                return $this->redirect($target);
            } else {
                $this->Flash->error('Bitte verifiziere zuerst deine E-Mail Adresse!');
                return $this->redirect(['action' => 'logout']);
            }

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
                $this->getMailer('User')->send('notify', [$user]);
                $this->Flash->success('Dein Account wurde freigeschalten!');
            } else {
                $this->Flash->error('Es gab einen Fehler beim speichern!');
            }
        } else {
            $this->Flash->error('Der Hash ist nicht korrekt');
        }
        return $this->redirect(['controller' => 'users', 'action' => 'login']);

    }

    public function manage()
    {
        $users = $this->paginate($this->Users, ['contain' => ['Roles'], 'order' => ['Users.id']]);
        $this->Authorization->authorize($this->Users->find()->first(), 'manage');

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData('user');
            $user = $this->Users->get($data['id']);
            $user = $this->Users->patchEntity($user, $data);
            if ($this->Users->save($user)) {
                $this->Flash->success('Benutzer gespeichert');
                return $this->redirect(['action' => 'manage']);
            } else {
                $this->Flash->error('Benutzer konnte nicht gespeichert werden');
            }
        }

        $options = $this->Users->Roles->find('list');
        $this->set(compact('users', 'options'));
    }

    public function userAction($id = null, $action = null, $additionalData = [])
    {
        if ($id !== null) {
            switch ($action) {
                case 'sendmail':
                    $user = $this->Users->get($id);
                    if ($user) {
                        $this->getMailer('User')->send('register', [$user]);
                    }
                    break;
                default:
                    throw new BadRequestException();
            }
        }
    }

}
