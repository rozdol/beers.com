<?php
use Migrations\AbstractMigration;

class OptimizationsOnLogAudit extends AbstractMigration
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
        $table = $this->table('log_audit');

        $table->addColumn('user_id', 'uuid', [
            'default' => null,
            'null' => true,
        ]);

        // Change timestamp type to dateTime
        $table->changeColumn('timestamp', 'datetime');

        // Add indexes
        $table->addIndex([
            'user_id',
        ], [
            'name' => 'BY_USER_ID',
            'unique' => false,
        ]);
        $table->addIndex([
            'user_id', 'primary_key',
        ], [
            'name' => 'BY_USER_ID_AND_PRIMARY_KEY',
            'unique' => false,
        ]);
        $table->addIndex([
            'timestamp',
        ], [
            'name' => 'BY_TIMESTAMP',
            'unique' => false,
        ]);
        $table->addIndex([
            'primary_key', 'source'
        ], [
            'name' => 'BY_PRIMARY_KEY_AND_SOURCE',
            'unique' => false,
        ]);

        $table->update();
    }
}
