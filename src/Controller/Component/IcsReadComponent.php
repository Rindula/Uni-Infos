<?php
declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * Ics component
 */
class IcsReadComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function initialize(array $config): void
    {
        parent::initialize($config);
    }

    public function getIcsEventsAsArray($icalString)
    {
        $icsDates = array();
        /* Explode the ICs Data to get datas as array according to string ‘BEGIN:’ */
        $icsData = explode("BEGIN:", $icalString);
        /* Iterating the icsData value to make all the start end dates as sub array */
        foreach ($icsData as $key => $value) {
            $icsDatesMeta [$key] = explode("\n", $value);
        }
        /* Itearting the Ics Meta Value */
        foreach ($icsDatesMeta as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                /* to get ics events in proper order */
                $icsDates = $this->getICSDates($key, $subKey, $subValue, $icsDates);
            }
        }
        return $icsDates;
    }

    /* funcion is to avaid the elements wich is not having the proper start, end  and summary informations */
    private function getICSDates($key, $subKey, $subValue, $icsDates)
    {
        if ($key != 0 && $subKey == 0) {
            $icsDates [$key] ["BEGIN"] = $subValue;
        } else {
            $subValueArr = explode(":", $subValue, 2);
            if (isset ($subValueArr [1])) {
                $icsDates [$key] [$subValueArr [0]] = $subValueArr [1];
            }
        }
        return $icsDates;
    }
}
