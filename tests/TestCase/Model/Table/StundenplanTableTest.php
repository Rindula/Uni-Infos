<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\StundenplanTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\StundenplanTable Test Case
 */
class StundenplanTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var StundenplanTable
     */
    protected $Stundenplan;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Stundenplan',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Stundenplan') ? [] : ['className' => StundenplanTable::class];
        $this->Stundenplan = TableRegistry::getTableLocator()->get('Stundenplan', $config);
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
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findByUid method
     *
     * @return void
     */
    public function testFindByUid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
