<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\Component\IcsReadComponent;
use App\Controller\Component\IcsWriteComponent;
use App\Model\Entity\Stundenplan;
use Authentication\Controller\Component\AuthenticationComponent;
use Authorization\Controller\Component\AuthorizationComponent;
use Cake\Cache\Cache;
use Cake\Datasource\RepositoryInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\Response;
use Cake\I18n\Time;

/**
 * Stundenplan Controller
 *
 *
 * @method Stundenplan[]|ResultSetInterface paginate($object = null, array $settings = [])
 * @property Component\IcsReadComponent|RepositoryInterface|IcsReadComponent|null IcsRead
 * @property RepositoryInterface|null Stundenplan
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
        $this->Authorization->skipAuthorization();
        $this->Authentication->allowUnauthenticated(['index', 'ajax', 'calendar']);
    }

    /**
     * Index method
     *
     * @return Response|null
     */
    public function index()
    {
        $courses = $this->getCourses(true);
        $courseSelected = (isset($_COOKIE["selectedCourse"])) ? $_COOKIE["selectedCourse"] : "inf19b";
        $this->set(compact('courses', 'courseSelected'));
    }

    public function ajax($course = 'inf19b', $all = false, $showVorlesung = false)
    {
//        $this->autoRender = false;
        $this->viewBuilder()->setLayout('ajax');
        $events = $this->fetchCalendar($course, $all, $showVorlesung);

        $this->set(compact('events'));
        $this->response = $this->response->cors($this->request)->allowOrigin('*')->allowMethods(['GET'])->build();
    }

    public function calendar($course = null)
    {
        $this->viewBuilder()->setLayout('ajax');
        if ($course) {
            $events = $this->fetchCalendar($course, true, false);
            $icsWriter = $this->IcsWrite;
            foreach ($events as $event) {
                $icsWriter->newEvent($event['SUMMARY'], $event['custom']['begin']['timestamp'], $event['custom']['end']['timestamp'], $event['DESCRIPTION'], $event['LOCATION']);
            }
            $this->set('writer', $icsWriter);
            return;
        }

        $this->set('writer', $this->IcsWrite);
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
        $stundenplan = $this->Stundenplan->find('all')->where([
            'uid IS' => $data['uid']
        ])->select([
            'uid',
            'note',
            'info_for_db',
        ])->first();

        if (!$stundenplan) {
            $stundenplan = $this->Stundenplan->newEmptyEntity();
            $data['note'] = '';
        }

        $stundenplan = $this->Stundenplan->patchEntity($stundenplan, $data);
        $this->Stundenplan->save($stundenplan);

        return $stundenplan;
    }

    /**
     * @param $course
     * @param $all
     * @param $showVorlesung
     * @return array
     */
    private function fetchCalendar($course, $all, $showVorlesung)
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
        foreach ($events as $key => &$event) {

            if (!empty($event['SUMMARY']) && $event['SUMMARY'] == "Studientag") {
                unset($events[$key]);
                continue;
            }

            if (!empty($last)) {
                $begin = new Time($event['DTSTART;TZID=Europe/Berlin']);
                if ($begin->diffInSeconds($last['time']) < 5) {
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
            if (!empty($event['SUMMARY']) && strpos($event['SUMMARY'], 'Klausur')) {
                $event['custom']['isKlausur'] = true;
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

            $dbEvent = $this->saveToDatabase($event);

            if (!empty($dbEvent->note)) {
                $event['custom']['note'] = $dbEvent->note;
            }

            $last = ['key' => $key, 'name' => $event['SUMMARY'], 'time' => new Time($event['DTSTART;TZID=Europe/Berlin'])];
        }
        return $events;
    }

    private function getCourses($grouped = false)
    {
        Cache::enable();
        if (($coursesJson = Cache::read('courses', 'longTerm')) === null) {
            $coursesJson = file_get_contents("https://stuv-mosbach.de/survival/api.php?action=getCourses");
            Cache::write('courses', $coursesJson, 'longTerm');
        }
        $courses = json_decode($coursesJson);
        foreach ($courses as $key => &$course) {
            $course = preg_filter("/(([-a-zA-Z]+)\d+\w?)/", '$0', $course);
            if (empty($course)) unset($courses[$key]);
        }
        sort($courses);
        if (!$grouped) return $courses;

        $courseGroup = [];

        foreach ($courses as $key => &$course) {
            $courseGroup[preg_filter("/(([-a-zA-Z]+)\d+\w?)/", '$2', $course)][strtolower(preg_filter("/(([-a-zA-Z]+)\d+\w?)/", '$1', $course))] = preg_filter("/(([-a-zA-Z]+)\d+\w?)/", '$0', $course);
            if (empty($course)) unset($courses[$key]);
        }

        return $courseGroup;
    }

}
