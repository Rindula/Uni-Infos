<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\StundenplanController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

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
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->markTestSkipped('Nicht notwendig');
    }

    /**
     * Test ajax method
     *
     * @return void
     */
    public function testAjax(): void
    {
        $this->markAsRisky();
    }
}
