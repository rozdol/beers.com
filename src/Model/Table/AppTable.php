<?php
namespace App\Model\Table;

use CsvMigrations\Table;

/**
 * App Model
 */
class AppTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('AuditStash.AuditLog', [
            'blacklist' => ['created', 'modified', 'created_by', 'modified_by']
        ]);
    }
}
