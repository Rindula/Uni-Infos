<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Stundenplan;
use Cake\Cache\Cache;
use Cake\Datasource\RepositoryInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\I18n\Time;
use Ics;
use IcsComponent;

/**
 * Stundenplan Controller
 *
 *
 * @method Stundenplan[]|ResultSetInterface paginate($object = null, array $settings = [])
 * @property Component\IcsComponent|\Cake\Controller\Component\IcsComponent|RepositoryInterface|Ics|IcsComponent|null Ics
 * @property RepositoryInterface|null Stundenplan
 */
class StundenplanController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Ics');
    }

    /**
     * Index method
     *
     * @return Response|null
     */
    public function index()
    {
        $courses = [
            'inf19a' => 'INF19A',
            'inf19b' => 'INF19B',
            'Sonderwünsche' => [
                'ht19' => 'HT19',
                'wi17b' => 'WI17B',
            ]
        ];
        $courseSelected = (isset($_COOKIE["selectedCourse"])) ? $_COOKIE["selectedCourse"] : "inf19b";
        $this->set(compact('courses', 'courseSelected'));
    }

    public function ajax($course = 'inf19b', $all = false, $showVorlesung = false)
    {
        $this->autoRender = false;
        Cache::enable();
        if (($icsString = Cache::read('icsString' . $course, 'shortTerm')) === null) {
            $icsString = file_get_contents("http://ics.mosbach.dhbw.de/ics/$course.ics");
            Cache::write('icsString' . $course, $icsString, 'shortTerm');
        }
        $cal = $this->Ics;
        $events = $cal->getIcsEventsAsArray($icsString);
        $last = null;
        foreach ($events as $key => &$event) {

            if (!empty($event['SUMMARY']) && ($event['SUMMARY'] == "Studientag" && !$all)) {
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
                $event['custom']['begin']['nice'] = (new Time($event['DTSTART;TZID=Europe/Berlin']))->nice();
                $event['custom']['begin']['date'] = (new Time($event['DTSTART;TZID=Europe/Berlin']))->toDateTimeString();
                $event['custom']['begin']['words'] = (new Time($event['DTSTART;TZID=Europe/Berlin']))->timeAgoInWords(['format' => 'MMM d, YYY']);
                $event['custom']['begin']['isFuture'] = (new Time($event['DTSTART;TZID=Europe/Berlin']))->isFuture();
                $event['custom']['begin']['isPast'] = (new Time($event['DTSTART;TZID=Europe/Berlin']))->isPast();
                $event['custom']['begin']['timestamp'] = (new Time($event['DTSTART;TZID=Europe/Berlin']))->toUnixString();
                $event['custom']['begin']['isToday'] = (new Time($event['DTSTART;TZID=Europe/Berlin']))->isToday();
                $event['custom']['begin']['isTomorrow'] = (new Time($event['DTSTART;TZID=Europe/Berlin']))->isTomorrow();
            }
            if (!empty($event['DTEND;TZID=Europe/Berlin'])) {
                $event['custom']['end']['nice'] = (new Time($event['DTEND;TZID=Europe/Berlin']))->nice();
                $event['custom']['end']['words'] = (new Time($event['DTEND;TZID=Europe/Berlin']))->timeAgoInWords(['format' => 'MMM d, YYY']);
                $event['custom']['end']['isFuture'] = (new Time($event['DTEND;TZID=Europe/Berlin']))->isFuture();
                $event['custom']['end']['isPast'] = (new Time($event['DTEND;TZID=Europe/Berlin']))->isPast();
                $event['custom']['end']['timestamp'] = (new Time($event['DTEND;TZID=Europe/Berlin']))->toUnixString();
                $event['custom']['end']['isToday'] = (new Time($event['DTEND;TZID=Europe/Berlin']))->isToday();
            }

//            (new Time())->

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

        echo json_encode($events);
        $this->response->cors($this->request)->allowOrigin('*')->build();
        exit;
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

}
