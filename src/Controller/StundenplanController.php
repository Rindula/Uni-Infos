<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Cache\Cache;
use Cake\I18n\Time;

/**
 * Stundenplan Controller
 *
 *
 * @method \App\Model\Entity\Stundenplan[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 * @property Component\IcsComponent|\Cake\Controller\Component\IcsComponent|\Cake\Datasource\RepositoryInterface|\Ics|\IcsComponent|null Ics
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
     * @return \Cake\Http\Response|null
     */
    public function index()
    {

    }

    public function ajax()
    {
        $this->viewBuilder()->setLayout("ajax");
        Cache::enable();
        if (($icsString = Cache::read('icsString', 'shortTerm')) === null) {
            $icsString = file_get_contents('http://ics.mosbach.dhbw.de/ics/inf19b.ics');
            Cache::write('icsString', $icsString, 'shortTerm');
        }
        $cal = $this->Ics;
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

            if (empty($event['SUMMARY']) || (!empty($event['custom']['end']) && $event['custom']['end']['isPast'])) {
                unset($events[$key]);
                continue;
            }

            $last = ['key' => $key, 'name' => $event['SUMMARY'], 'time' => new Time($event['DTSTART;TZID=Europe/Berlin'])];
        }

        $this->set(compact('events'));
    }

}
