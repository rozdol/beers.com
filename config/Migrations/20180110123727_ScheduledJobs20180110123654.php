<?php
use CsvMigrations\CsvMigration;

class ScheduledJobs20180110123654 extends CsvMigration
{
        public function change()
    {
        $table = $this->table('scheduled_jobs');
        $table = $this->csv($table);

        if (!$this->hasTable('scheduled_jobs')) {
            $table->create();
        } else {
            $table->update();
        }

        $joinedTables = $this->joins('scheduled_jobs');
        if (!empty($joinedTables)) {
            foreach ($joinedTables as $joinedTable) {
                $joinedTable->create();
            }
        }
    }
}
