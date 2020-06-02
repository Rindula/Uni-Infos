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
                $this->Flash->error('Bitte verifiziere zuerst deine E-Mail Adresse! Wenn du keine E-Mail bekommen hast, melde das bitte an webmaster@rindula.de');
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

        if ($user->enabled == null && $checkHash === $hash) {
            $user->enabled = new Time();
            if ($this->Users->save($user)) {
                $this->getMailer('User')->send('notify', [$user]);
                $this->Flash->success('Dein Account wurde freigeschalten!');
            } else {
                $this->Flash->error('Es gab einen Fehler beim speichern!');
            }
        } elseif ($user->enabled != null) {
            $this->Flash->error('Die Verifizierung ist bereits abgeschlossen!');
        } else {
            $this->Flash->error('Der Link ist nicht korrekt!');
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
        $this->Authorization->authorize($this->Users->get($this->Authentication->getIdentity()->id), 'manage');
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

    public function preferences()
    {
        $user = $this->Users->get($this->Authentication->getIdentity()->id);
        $this->Authorization->authorize($user);

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            if (isset($data['password'], $data['passwordConfirm'])) {
                $user = $this->Users->patchEntity($user, $data, ['validate' => 'password']);
                if ($this->Users->save($user)) {
                    $this->Flash->success(__('Password has been updated'));
                    return $this->redirect(['action' => 'preferences']);
                } else {
                    $this->Flash->error('There was an error saving the password!');
                }
            }
            if (isset($data['course'])) {
                $user = $this->Users->patchEntity($user, $data, ['validate' => 'course']);
                if ($this->Users->save($user)) {
                    $this->Flash->success(__('Your Course has been updated'));
                    return $this->redirect(['action' => 'preferences']);
                } else {
                    $this->Flash->error(__('There was an error updating your course!'));
                }
            }
        }

        $courses = getCourses(true);

        $this->set(compact('user', 'courses'));
    }

    public function disable($id = null)
    {
        $this->autoRender = false;
        $currentUser = ($id == null) ? $this->Users->get($this->Authentication->getIdentity()->id) : $this->Users->get($id);
        $this->Authorization->authorize($currentUser);
        $currentUser->enabled = null;
        $this->Users->saveOrFail($currentUser);
        if ($currentUser->id == $this->Authentication->getIdentity()->id) return $this->logout();
        return $this->redirect(['action' => 'manage']);
    }

}
