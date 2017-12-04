<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

/**
 * AddDblistPermissions shell command.
 */
class AddDblistPermissionsShell extends Shell
{

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        return $parser;
    }

    /**
     * main() method.
     *
     * @return void
     */
    public function main()
    {
        $this->out("Shell: Add Dblist 'list' capability to 'Everyone' role");
        $this->hr();

        $roleId = null;
        $targetRoleName = 'Everyone';

        $capsToAdd = [
            'cap__CsvMigrations_Controller_DblistsController__index',
            'cap__CsvMigrations_Controller_DblistItemsController__index',
        ];

        $rolesTable = TableRegistry::get('RolesCapabilities.Roles');
        $capTable = TableRegistry::get('RolesCapabilities.Capabilities');

        $role = $rolesTable->find()
            ->where(['name' => $targetRoleName])
            ->first();

        if (!empty($role)) {
            foreach ($capsToAdd as $capability) {
                $result = $capTable->find()
                    ->where(
                        [
                            'name' => $capability,
                            'role_id' => $role->id,
                        ]
                    )->first();

                //save capability if it doesn't exist for 'everyone' role.
                if (!$result) {
                    $capEntity = $capTable->newEntity();
                    $capEntity->name = $capability;
                    $capEntity->role_id = $role->id;
                    if ($capTable->save($capEntity)) {
                        $this->out("<info>Capability [$capability] added to role [{$role->name}]</info>");
                    }
                } else {
                    $this->out("<info>Capability [$capability] for role [{$role->name}] already exists.</info>");
                }
            }
        }

        $this->out("<success>Roles adding completed.</success>");
    }
}
