<?php
namespace App\Shell\Task;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

class SchemaTask extends Shell
{
    const KEY_SCHEMA = 'schema';
    const KEY_CSV_DEFS = 'csvDefinitions';

    /**
     * Main entry point
     *
     * @return void
     */
    public function main()
    {
    }

    /**
     * Get all database tables
     *
     * @return array
     */
    public function getAllTables()
    {
        $result = ConnectionManager::get('default')
            ->schemaCollection()
            ->listTables();

        return $result;
    }

    /**
     * Get schema definitions for a given table
     *
     * @param string|array $tables One or more table names
     * @return array
     */
    public function getTableSchema($tables)
    {
        $result = [];

        if (empty($tables)) {
            return $result;
        }

        if (is_string($tables)) {
            $tables = [ $tables ];
        }

        foreach ($tables as $table) {
            $result[$table][self::KEY_SCHEMA] = ConnectionManager::get('default')
                ->schemaCollection()
                ->describe($table);
        }

        return $result;
    }

    /**
     * Get CSV field definitions for given tables
     *
     * @param array $tables List of tables
     * @return array
     */
    public function getTableCsvDefinitions(array $tables)
    {
        $result = [];

        if (empty($tables)) {
            return $result;
        }

        //foreach ($tables as $table => $columns) {
        foreach ($tables as $table) {
            $tableName = Inflector::camelize($table);
            $defs = [];
            try {
                $tableObject = TableRegistry::get($tableName);
                $defs = $tableObject->getFieldsDefinitions($tableName);
            } catch (\Exception $e) {
                // Skip non-CSV based table
            }
            $result[$table][self::KEY_CSV_DEFS] = $defs;
        }

        return $result;
    }

    /**
     * Check if a given table has given column
     *
     * @param string $table Table name
     * @param string $column Column name
     * @return bool True if has, false otherwise
     */
    public function hasTableColumn($table, $column)
    {
        $result = false;

        $table = (string)$table;
        $column = (string)$column;

        if (empty($table) || empty($column)) {
            throw new \InvalidArgumentException("Table and column are required parameters");
        }
        $schema = $this->getTableSchema($table);
        $columns = $schema[$table][self::KEY_SCHEMA]->columns();
        $result = in_array($column, $columns);

        return $result;
    }
}
