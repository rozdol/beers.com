<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LogAuditTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LogAuditTable Test Case
 */
class LogAuditTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\LogAuditTable
     */
    public $LogAudit;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.audit_logs'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('LogAudit') ? [] : ['className' => 'App\Model\Table\LogAuditTable'];
        $this->LogAudit = TableRegistry::get('LogAudit', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->LogAudit);

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
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
