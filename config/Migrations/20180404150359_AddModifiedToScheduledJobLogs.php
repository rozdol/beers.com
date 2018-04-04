<?php
use Migrations\AbstractMigration;

class AddModifiedToScheduledJobLogs extends AbstractMigration
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
        $table = $this->table('scheduled_job_logs');
        $table->addColumn('modified', 'datetime', [
           'default' => null,
           'null' => false,
           'after' => 'created',
        ]);
        $table->update();
    }
}
