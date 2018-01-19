<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ScheduledJobLogsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ScheduledJobLogsTable Test Case
 */
class ScheduledJobLogsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ScheduledJobLogsTable
     */
    public $ScheduledJobLogs;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.scheduled_job_logs',
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
        $config = TableRegistry::exists('ScheduledJobLogs') ? [] : ['className' => ScheduledJobLogsTable::class];
        $this->ScheduledJobLogs = TableRegistry::get('ScheduledJobLogs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ScheduledJobLogs);

        parent::tearDown();
    }
}
