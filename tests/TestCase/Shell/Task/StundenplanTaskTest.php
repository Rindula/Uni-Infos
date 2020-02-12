<?php
declare(strict_types=1);

namespace App\Test\TestCase\Shell\Task;

use App\Shell\Task\StundenplanTask;
use Cake\TestSuite\TestCase;

/**
 * App\Shell\Task\StundenplanTask Test Case
 */
class StundenplanTaskTest extends TestCase
{
    /**
     * ConsoleIo mock
     *
     * @var \Cake\Console\ConsoleIo|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $io;

    /**
     * Test subject
     *
     * @var \App\Shell\Task\StundenplanTask
     */
    protected $Stundenplan;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();
        $this->Stundenplan = new StundenplanTask($this->io);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Stundenplan);

        parent::tearDown();
    }

    /**
     * Test getOptionParser method
     *
     * @return void
     */
    public function testGetOptionParser(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test main method
     *
     * @return void
     */
    public function testMain(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
