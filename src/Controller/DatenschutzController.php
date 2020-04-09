<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Datenschutz;
use Authentication\Controller\Component\AuthenticationComponent;
use Authorization\Controller\Component\AuthorizationComponent;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\Response;

/**
 * Datenschutz Controller
 *
 *
 * @method Datenschutz[]|ResultSetInterface paginate($object = null, array $settings = [])
 * @property AuthorizationComponent|null Authorization
 * @property AuthenticationComponent|null Authentication
 */
class DatenschutzController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->addUnauthenticatedActions(['index', 'impressum']);
    }

    /**
     * Index method
     *
     * @return Response|null|void Renders view
     */
    public function index()
    {
        $this->Authorization->skipAuthorization();
    }

    /**
     * Index method
     *
     * @return Response|null|void Renders view
     */
    public function impressum()
    {
        $this->Authorization->skipAuthorization();
    }
}
