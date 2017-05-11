<?php
use Migrations\AbstractMigration;

class AllowViewDblistsToEveryone extends AbstractMigration
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
        //NOTE: code moved to src/Shell/CheckDblistPermissions.
        //Migration left intentionally for backward-compatibility reasons.
    }
}
