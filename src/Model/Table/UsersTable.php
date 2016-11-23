<?php
namespace App\Model\Table;

use CakeDC\Users\Model\Table\UsersTable as Table;
use CsvMigrations\ConfigurationTrait;

/**
 * Users Model
 */
class UsersTable extends Table
{
    use ConfigurationTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        // set table/module configuration
        $this->_setConfiguration($this->table());
    }
}
