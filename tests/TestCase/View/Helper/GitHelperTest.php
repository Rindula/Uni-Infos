<?php
declare(strict_types=1);

namespace App\Test\TestCase\View\Helper;

use App\View\Helper\GitHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * App\View\Helper\GitHelper Test Case
 */
class GitHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var GitHelper
     */
    protected $Git;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $view = new View();
        $this->Git = new GitHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Git);

        parent::tearDown();
    }
}
