<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\Component\IcsReadComponent;
use App\Controller\Component\IcsWriteComponent;
use App\Form\CalendarConfiguratorForm;
use App\Model\Entity\Stundenplan;
use App\Model\Table\StundenplanTable;
use Authentication\Controller\Component\AuthenticationComponent;
use Authorization\Controller\Component\AuthorizationComponent;
use Cake\Cache\Cache;
use Cake\Datasource\RepositoryInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\Response;
use Cake\I18n\Time;
use Cake\Routing\Router;
use Cake\View\Helper\HtmlHelper;
use Cake\View\Helper\TextHelper;
use Cake\View\View;

/**
 * Stundenplan Controller
 *
 *
 * @method Stundenplan[]|ResultSetInterface paginate($object = null, array $settings = [])
 * @property Component\IcsReadComponent|RepositoryInterface|IcsReadComponent|null IcsRead
 * @property StundenplanTable|null Stundenplan
 * @property Component\IcsWriteComponent|RepositoryInterface|IcsWriteComponent|null IcsWrite
 * @property AuthorizationComponent|null Authorization
 * @property AuthenticationComponent|null Authentication
 */
class StundenplanController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('IcsRead');
        $this->loadComponent('IcsWrite');
        $this->Authentication->allowUnauthenticated(['index', 'api', 'calendar', 'configureCalendarLink']);
    }

    /**
     * Index method
     *
     * @return Response|null
     */
    public function index()
    {
        $this->Authorization->skipAuthorization();
        $courses = getCourses(true);
        $courseSelected = (isset($_COOKIE["selectedCourse"])) ? $_COOKIE["selectedCourse"] : "";
        $this->set(compact('courses', 'courseSelected'));
    }

    public function api($course = '', $all = false, $showVorlesung = false, $showSeminar = false, $onlineOnly = false)
    {
        $this->Authorization->skipAuthorization();
        $this->getResponse()->cors($this->getRequest(), '*');
        $this->viewBuilder()->setLayout('ajax');
        if (!empty($course)) {
            $events = $this->fetchCalendar($course, $all, $showVorlesung, $showSeminar);
        } else {
            $events = [
                [
                    'SUMMARY' => 'Kein Kurs ausgewählt',
                    'LOCATION' => '---',
                    'DESCRIPTION' => 'Bitte nutze das Dropdown Menü, um einen Kurs auszuwählen.',
                    'custom' => [
                        'begin' => [
                            'date' => (new Time())->toTimeString(),
                            'nice' => (new Time())->nice(),
                            'words' => (new Time())->timeAgoInWords(),
                            'timestamp' => (new Time())->toUnixString(),
                            'isPast' => false,
                        ],
                        'end' => [
                            'date' => (new Time())->toTimeString(),
                            'nice' => (new Time())->nice(),
                            'words' => (new Time())->timeAgoInWords(),
                            'timestamp' => (new Time())->toUnixString(),
                            'isPast' => false,
                        ],
                        'dayid' => (new Time())->format('Ymd'),
                        'current' => false,
                        'today' => true,
                        'tomorrow' => false,
                        'isSeminar' => false,
                        'isKlausur' => false,
                        'can_edit' => false,
                        'can_delete' => false,
                    ]
                ]
            ];
        }

        if ($onlineOnly) {
            foreach ($events as $key => $event) {
                if (!$event['custom']['isOnline']) {
                    unset($events[$key]);
                }
            }
        }

        $this->set(compact('events'));
        $this->response = $this->response->cors($this->request)->allowOrigin('*')->allowMethods(['GET'])->build();
    }

    /**
     * @param $course
     * @param $all
     * @param $showVorlesung
     * @return array
     */
    private function fetchCalendar($course, $all, $showVorlesung, $showSeminar)
    {
        $course = strtolower($course);
        Cache::enable();
        if (($icsString = Cache::read('icsString' . $course, 'shortTerm')) === null) {
            $icsString = file_get_contents("http://ics.mosbach.dhbw.de/ics/$course.ics");
            Cache::write('icsString' . $course, $icsString, 'shortTerm');
        }
        $cal = $this->IcsRead;
        $events = $cal->getIcsEventsAsArray($icsString);
        $last = null;
        $htmlHelper = (new HtmlHelper(new View()));
        $textHelper = (new TextHelper(new View()));
        foreach ($events as $key => &$event) {

            if (!empty($event['SUMMARY']) && $event['SUMMARY'] == "Studientag") {
                unset($events[$key]);
                continue;
            }

            if (!empty($last)) {
                $begin = new Time($event['DTSTART;TZID=Europe/Berlin']);
                if ($begin->diffInSeconds($last['time'], true) < 5) {
                    $events[$last['key']]['LOCATION'] .= ' / ' . $event['LOCATION'];
                    unset($events[$key]);
                    continue;
                }
            }

            if (empty($event['LOCATION'])) {
                $event['LOCATION'] = '';
            }
            if (empty($event['DESCRIPTION'])) {
                $event['DESCRIPTION'] = '';
            }

            $event['custom']['isKlausur'] = false;
            if (!empty($event['SUMMARY']) && (strpos($event['SUMMARY'], 'Klausur') !== false || strpos($event['SUMMARY'], 'Praxismodul') !== false)) {
                $event['custom']['isKlausur'] = true;
            }

            $event['custom']['isSeminar'] = false;
            if (!empty($event['SUMMARY']) && strpos($event['SUMMARY'], 'Seminar')) {
                $event['custom']['isSeminar'] = true;
            }

            if (!empty($event['DTSTART;TZID=Europe/Berlin'])) {
                $startTime = new Time($event['DTSTART;TZID=Europe/Berlin']);
                $event['custom']['begin']['nice'] = $startTime->nice();
                $event['custom']['begin']['date'] = $startTime->toDateTimeString();
                $event['custom']['begin']['words'] = $startTime->timeAgoInWords(['format' => 'MMM d, YYY']);
                $event['custom']['begin']['isFuture'] = $startTime->isFuture();
                $event['custom']['begin']['isPast'] = $startTime->isPast();
                $event['custom']['begin']['timestamp'] = $startTime->toUnixString();
                $event['custom']['begin']['isToday'] = $startTime->isToday();
                $event['custom']['begin']['isTomorrow'] = $startTime->isTomorrow();
                $event['custom']['dayid'] = $startTime->format("Ymd");
            }
            if (!empty($event['DTEND;TZID=Europe/Berlin'])) {
                $endTime = new Time($event['DTEND;TZID=Europe/Berlin']);
                $event['custom']['end']['nice'] = $endTime->nice();
                $event['custom']['end']['words'] = $endTime->timeAgoInWords(['format' => 'MMM d, YYY']);
                $event['custom']['end']['isFuture'] = $endTime->isFuture();
                $event['custom']['end']['isPast'] = $endTime->isPast();
                $event['custom']['end']['timestamp'] = $endTime->toUnixString();
                $event['custom']['end']['isToday'] = $endTime->isToday();
                $event['custom']['end']['isTomorrow'] = $endTime->isTomorrow();
            }

            $event['custom']['current'] = false;
            $event['custom']['today'] = false;
            $event['custom']['tomorrow'] = false;

            if ((!empty($event['custom']['end']) && $event['custom']['end']['isFuture']) && (!empty($event['custom']['begin']) && $event['custom']['begin']['isPast'])) {
                $event['custom']['current'] = true;
            }
            if ((!empty($event['custom']['begin']) && $event['custom']['begin']['isToday'])) {
                $event['custom']['today'] = true;
            }
            if ((!empty($event['custom']['begin']) && $event['custom']['begin']['isTomorrow'])) {
                $event['custom']['tomorrow'] = true;
            }

            if (empty($event['SUMMARY']) || ((!empty($event['custom']['end']) && $event['custom']['end']['isPast']) && !$all)) {
                unset($events[$key]);
                continue;
            }

            if (!$showVorlesung) {
                $event['SUMMARY'] = str_replace(" Vorlesung", "", $event['SUMMARY']);
            }

            if (!$showSeminar) {
                $event['SUMMARY'] = str_replace(" Seminar", "", $event['SUMMARY']);
            }

            $dbEvent = $this->saveToDatabase($event);

            if (!empty($dbEvent->note)) {
                $event['custom']['note'] = preg_replace('/(<a[^>]*?)>([^<]*)/i', '${1} target="_blank">*Link*', $textHelper->autoParagraph($textHelper->autoLink($dbEvent->note)));
            }
            if (!empty($dbEvent->loggedInNote) && $this->Authentication->getIdentity() && $this->Authorization->can($dbEvent, 'readNote')) {
                $event['custom']['loggedInNote'] = preg_replace('/(<a[^>]*?)>([^<]*)/i', '${1} target="_blank">*Link*', $textHelper->autoParagraph($textHelper->autoLink($dbEvent->loggedInNote)));
            }
            $event['custom']['can_edit'] = ($this->Authentication->getIdentity() && $this->Authorization->can($dbEvent, 'update')) ? $dbEvent->uid : false;
            $event['custom']['can_delete'] = ((!empty($dbEvent->loggedInNote) || !empty($dbEvent->note)) && $this->Authentication->getIdentity() && $this->Authorization->can($dbEvent, 'delete')) ? (!empty($dbEvent->note) ? $htmlHelper->link('Notiz löschen', ['controller' => 'stundenplan', 'action' => 'delete', $dbEvent->uid, 'note'], ['confirm' => 'Bist du sicher, dass du die Notiz löschen willst?']) . "<br>" : '') . ((!empty($dbEvent->loggedInNote)) ? $htmlHelper->link('Eingeloggten Notiz löschen', ['controller' => 'stundenplan', 'action' => 'delete', $dbEvent->uid, 'loggedInNote'], ['confirm' => 'Bist du sicher, dass du die Eingeloggten Notiz löschen willst?']) . "<br>" : '') . $htmlHelper->link('Alle Notizen löschen', ['controller' => 'stundenplan', 'action' => 'delete', $dbEvent->uid, 'all'], ['confirm' => 'Bist du sicher, dass du die alle Notizen dieser Stunde löschen willst?']) : false;
            $event['custom']['isOnline'] = $dbEvent->isOnline;

            $last = ['key' => $key, 'name' => $event['SUMMARY'], 'time' => new Time($event['DTSTART;TZID=Europe/Berlin'])];
        }
        return $events;
    }

    /**
     * @param $event
     * @return Stundenplan
     */
    private function saveToDatabase($event)
    {
        $data = [
            'uid' => $event['UID'],
            'info_for_db' => $event['custom']['begin']['nice'] . ' - ' . $event['SUMMARY'],
        ];
        /** @var Stundenplan $stundenplan */
        $stundenplan = $this->Stundenplan->findByUid($data['uid'])->select([
            'uid',
            'note',
            'loggedInNote',
            'info_for_db',
            'isOnline',
        ])->first();

        if (!$stundenplan) {
            $stundenplan = $this->Stundenplan->newEmptyEntity();
            $data['note'] = null;
            $data['loggedInNote'] = null;
        }

        $stundenplan = $this->Stundenplan->patchEntity($stundenplan, $data);
        $this->Stundenplan->save($stundenplan);

        return $stundenplan;
    }

    public function edit($uid = null)
    {
        $stundenplan = $this->Stundenplan->findByUid($uid)->firstOrFail();
        $this->Authorization->authorize($stundenplan, 'update');

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            if (empty($data['note'])) $data['note'] = null;
            if (empty($data['loggedInNote'])) $data['loggedInNote'] = null;
            $this->Stundenplan->patchEntity($stundenplan, $data);
            if ($this->Stundenplan->save($stundenplan)) {
                $this->Flash->success('Notiz gespeichert');
                return $this->redirect(['controller' => 'stundenplan', 'action' => 'index']);
            }
            $this->Flash->error('Notiz konnte nicht gespeichert werden');
        }

        $this->set(compact('stundenplan'));
    }

    public function delete($uid = null, $type = 'all')
    {
        $stundenplan = $this->Stundenplan->findByUid($uid)->firstOrFail();
        $this->Authorization->authorize($stundenplan);

        $data = [];
        switch ($type) {
            case 'all':
                $data['note'] = null;
                $data['loggedInNote'] = null;
                break;
            case 'note':
                $data['note'] = null;
                break;
            case 'loggedInNote':
                $data['loggedInNote'] = null;
                break;
        }
        $this->Stundenplan->patchEntity($stundenplan, $data);
        if ($this->Stundenplan->save($stundenplan)) {
            $this->Flash->success('Die Notiz wurde erfolgreich entfernt');
        } else {
            $this->Flash->error('Die Notiz konnte nicht entfernt werden');
        }
        return $this->redirect(['controller' => 'stundenplan', 'action' => 'index']);
    }

    public function calendar($course = null, $onlineOnly = false)
    {
        $this->Authorization->skipAuthorization();
        $this->getResponse()->cors($this->getRequest(), '*');
        $this->viewBuilder()->setLayout('ajax');
        if ($course) {
            $events = $this->fetchCalendar($course, true, false, false);
            if ($onlineOnly) {
                foreach ($events as $key => $event) {
                    if (!$event['custom']['isOnline']) {
                        unset($events[$key]);
                    }
                }
            }
            $icsWriter = $this->IcsWrite;
            foreach ($events as $event) {
                $categories = [];
                if ($event['custom']['isKlausur']) $categories[] = "KLAUSUR";
                if ($event['custom']['isSeminar']) $categories[] = "SEMINAR";
                if (!($event['custom']['isKlausur'] || $event['custom']['isSeminar'])) $categories[] = "VORLESUNG";
                $description = $event['DESCRIPTION'];
                if (isset($event['custom']['note'])) {
                    $description .= PHP_EOL . PHP_EOL . strip_tags($event['custom']['note']);
                }
                $icsWriter->newEvent($event['SUMMARY'], $event['custom']['begin']['timestamp'], $event['custom']['end']['timestamp'], $description, $event['LOCATION'], $categories);
            }
            $this->set('writer', $icsWriter);
            return;
        }

        $this->set('writer', $this->IcsWrite);
    }

    public function configureCalendarLink()
    {
        $this->Authorization->skipAuthorization();
        $calendarConfigurator = new CalendarConfiguratorForm();
        $link = '';
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if ($calendarConfigurator->validate($data)) {
                $calendarConfigurator->setData($data);
                $link = Router::url(['_full' => true, 'controller' => 'stundenplan', 'action' => 'calendar', $data['course'], $data['onlineOnly']]);
                $this->Flash->success('Dein Kalenderlink wurde erstellt.');
            } else {
                $this->Flash->error('Es gab ein Problem beim erstellen des Links!');
            }
        }

        if ($this->request->is('get')) {
            $calendarConfigurator->setData([
                'course' => (isset($_COOKIE["selectedCourse"])) ? $_COOKIE["selectedCourse"] : "",
                'onlineOnly' => false,
            ]);
        }

        $courses = getCourses(true);

        $this->set(compact('calendarConfigurator', 'link', 'courses'));
    }

}
