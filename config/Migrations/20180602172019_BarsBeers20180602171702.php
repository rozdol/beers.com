<?php
use CsvMigrations\CsvMigration;

class BarsBeers20180602171702 extends CsvMigration
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
        $table = $this->table('bars_beers');
        $table = $this->csv($table);

        if (!$this->hasTable('bars_beers')) {
            $table->create();
        } else {
            $table->update();
        }
    }
}
