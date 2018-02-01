<?php
use Migrations\AbstractMigration;

class AddUserIdToLogAudit extends AbstractMigration
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
        $table->update();
    }
}
