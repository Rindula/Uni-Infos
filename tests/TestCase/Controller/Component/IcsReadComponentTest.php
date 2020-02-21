<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\IcsReadComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Component\IcsComponent Test Case
 */
class IcsReadComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var IcsReadComponent
     */
    protected $IcsComponent;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->IcsComponent = new IcsReadComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->IcsComponent);

        parent::tearDown();
    }

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
     * Test getIcsEventsAsArray method
     *
     * @return void
     */
    public function testGetIcsEventsAsArray(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
