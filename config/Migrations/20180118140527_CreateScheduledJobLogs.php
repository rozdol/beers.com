<?php
use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateScheduledJobLogs extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('scheduled_job_logs', ['id' => false, 'primary_key' => ['id']]);

        $table->addColumn('id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);

        $table->addColumn('scheduled_job_id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);

        $table->addColumn('context', 'string', [
            'default' => null,
            'null' => true,
        ]);

        $table->addColumn('extra', 'text', [
            'default' => null,
            'limit' => MysqlAdapter::TEXT_LONG,
            'null' => true,
        ]);

        $table->addColumn('status', 'string', [
            'default' => null,
            'null' => true
        ]);

        $table->addColumn('datetime', 'datetime', [
            'default' => null,
            'null' => false
        ]);

        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);

        $table->addPrimaryKey(['id']);

        $table->create();
    }
}
