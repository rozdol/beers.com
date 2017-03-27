<?php
use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddImageToUsers extends AbstractMigration
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
        $table = $this->table('users');
        $table->addColumn('image', 'blob', [
            'default' => null,
            'limit' => MysqlAdapter::BLOB_LONG,
            'null' => true,
        ]);
        $table->update();
    }
}
