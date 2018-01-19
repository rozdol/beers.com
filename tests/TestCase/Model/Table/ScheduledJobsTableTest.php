<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ScheduledJobsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ScheduledJobsTable Test Case
 */
class ScheduledJobsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ScheduledJobsTable
     */
    public $ScheduledJobsTable;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.scheduled_jobs'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('ScheduledJobs') ? [] : ['className' => ScheduledJobsTable::class];
        $this->ScheduledJobsTable = TableRegistry::get('ScheduledJobs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ScheduledJobsTable);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getActiveJobs method
     *
     * @return void
     */
    public function testGetActiveJobs()
    {
        $result = $this->ScheduledJobsTable->getActiveJobs();

        $this->assertNotEmpty($result);
        $this->assertInstanceOf('\Cake\ORM\ResultSet', $result);
    }
}
