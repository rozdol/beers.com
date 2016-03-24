<?php
use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateLogAudit extends AbstractMigration
{

    public $autoId = false;

    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('log_audit');
        $table->addColumn('id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('timestamp', 'timestamp', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('primary_key', 'uuid', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('source', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('parent_source', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('changed', 'text', [
            'default' => null,
            'limit' => MysqlAdapter::TEXT_LONG,
            'null' => true,
        ]);
        $table->addColumn('meta', 'text', [
            'default' => null,
            'limit' => MysqlAdapter::TEXT_LONG,
            'null' => true,
        ]);
        $table->addPrimaryKey([
            'id',
        ]);
        $table->create();
    }
}
