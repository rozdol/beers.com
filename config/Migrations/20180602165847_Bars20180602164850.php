<?php
use CsvMigrations\CsvMigration;

class Bars20180602164850 extends CsvMigration
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
        $table = $this->table('bars');
        $table = $this->csv($table);

        if (!$this->hasTable('bars')) {
            $table->create();
        } else {
            $table->update();
        }
    }
}
