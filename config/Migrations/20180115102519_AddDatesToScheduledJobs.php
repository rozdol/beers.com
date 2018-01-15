<?php
use Migrations\AbstractMigration;

class AddDatesToScheduledJobs extends AbstractMigration
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
        $table = $this->table('scheduled_jobs');
        $table->addColumn('start_date', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('end_date', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->update();
    }
}
