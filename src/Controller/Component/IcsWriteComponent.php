<?php
declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\I18n\Time;
use Cake\Utility\Security;

/**
 * IcsWrite component
 */
class IcsWriteComponent extends Component
{
    const DT_FORMAT = 'Ymd\THis\Z';
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];
    protected $properties = [];

    /** @var array */
    private $events = [];

    public function to_string()
    {
        $rows = $this->build_props();
        return implode(PHP_EOL, $rows);
    }

    private function build_props()
    {
        // Build ICS properties - add header
        $ics_props = array(
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//hacksw/handcal//NONSGML v1.0//DE',
            'CALSCALE:GREGORIAN'
        );


        /** @var Event $event */
        foreach ($this->events as $event) {
            $ics_props[] = $event->getIcsEvent();
        }

        // Build ICS properties - add footer
        $ics_props[] = 'END:VCALENDAR';

        return $ics_props;
    }

    public function newEvent($summary, $start, $end, $description = "", $location = "")
    {
        $event = new Event($summary, $start, $end, $description, $location);
        $this->events[] = $event;
        return $event;
    }
}

class Event
{
    const DT_FORMAT = 'Ymd\THis\Z';
    /** @var string */
    private $summary;
    /** @var Time */
    private $start;
    /** @var Time */
    private $end;
    /** @var string */
    private $description;
    /** @var string */
    private $location;

    public function __construct($summary, $start, $end, $description = "", $location = "")
    {
        $this->summary = $summary;
        $this->start = new Time($start);
        $this->end = new Time($end);
        $this->description = $description;
        $this->location = $location;
    }

    /**
     * @return String ICS Konformes Event
     */
    public function getIcsEvent()
    {
        $props = [];
        $props[] = "BEGIN:VEVENT";
        $props[] = "UID:" . $this->getUid();
        $props[] = "DTSTAMP:" . (new Time())->format(self::DT_FORMAT);
        $props[] = "DTSTART;TZID=Europe/Berlin:" . $this->getStart()->format(self::DT_FORMAT);
        $props[] = "DTEND;TZID=Europe/Berlin:" . $this->getEnd()->format(self::DT_FORMAT);
        $props[] = "SUMMARY:" . $this->getSummary();
        if (!empty($this->getLocation())) {
            $props[] = "LOCATION:" . $this->getLocation();
        }
        if (!empty($this->getDescription())) {
            $props[] = "DESCRIPTION:" . $this->getDescription();
        }
        $props[] = "END:VEVENT";
        return join(PHP_EOL, $props);
    }

    /**
     * @return Time
     */
    public function getStart(): Time
    {
        return $this->start->timezone("Europe/Berlin");
    }

    /**
     * @param Time $start
     */
    public function setStart(Time $start): void
    {
        $this->start = $start;
    }

    /**
     * @return Time
     */
    public function getEnd(): Time
    {
        return $this->end->timezone("Europe/Berlin");
    }

    /**
     * @param Time $end
     */
    public function setEnd(Time $end): void
    {
        $this->end = $end;
    }

    /**
     * @return string
     */
    public function getSummary(): string
    {
        return $this->escape_string($this->summary);
    }

    /**
     * @param string $summary
     */
    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
    }

    private function escape_string($str)
    {
        return preg_replace('/([\,;])/', '\\\$1', $str);
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->escape_string($this->location);
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->escape_string($this->description);
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->escape_string($this->url);
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    private function getUid()
    {
        return Security::encrypt($this->getStart()->toAtomString() . $this->getEnd()->toAtomString() . $this->getSummary(), 'randomCalendarUid', Security::getSalt());
    }

}
