<?php
use CsvMigrations\CsvMigration;

class Beers20180602114831 extends CsvMigration
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
        $table = $this->table('beers');
        $table = $this->csv($table);

        if (!$this->hasTable('beers')) {
            $table->create();
        } else {
            $table->update();
        }
    }
}
