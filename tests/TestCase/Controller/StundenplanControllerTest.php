<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\I18n\Date;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Exception;

/**
 * App\Controller\StundenplanController Test Case
 *
 * @uses \App\Controller\StundenplanController
 */
class StundenplanControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Stundenplan',
    ];


    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->get("/stundenplan");
        $this->assertResponseSuccess("Fehler beim laden des Stundenplans");
        $this->assertResponseContains("<div id=\"list\">");
    }

    /**
     * Test ajax method
     *
     * @return void
     */
    public function testAjax(): void
    {
        $year = new Date("last year");
        do {
            $this->configRequest([
                'headers' => ['Accept' => 'application/json']
            ]);
            $this->get("/stundenplan/api/inf" . $year->format("y") . "b/0/0/1/0");
            $this->assertResponseOk();
            $this->assertContentType("application/json");
            $response = json_decode($this->_getBodyAsString(), true);
            $year->modify("-1 years");
        } while (count($response) == 0 && $year->wasWithinLast("5 years"));
        try {
            while ($cal_element = array_shift($response)) {
                $this->assertArrayHasKey("DTSTART;TZID=Europe/Berlin", $cal_element);
                $this->assertArrayHasKey("DTEND;TZID=Europe/Berlin", $cal_element);
                $this->assertArrayHasKey("SUMMARY", $cal_element);
                $this->assertArrayHasKey("DESCRIPTION", $cal_element);
                $this->assertArrayHasKey("custom", $cal_element);
            }
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }
    }
}
