<?php
use Migrations\AbstractMigration;

use Cake\ORM\TableRegistry;

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
                    $capTable->save($capEntity);
                }
            }
        }
    }
}
