<?php
namespace App\Shell\Task;

use Cake\Datasource\ConnectionManager;
use Cake\Console\Shell;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

class SchemaTask extends Shell
{
    const KEY_SCHEMA = 'schema';
    const KEY_CSV_DEFS = 'csvDefinitions';

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

            //$updatedColumns = [];
            //foreach ($columns as $column => $properties) {
            //    $updatedColumns[$column] = $properties;
            //    if (!in_array($column, array_keys($defs))) {
            //        continue;
            //    }
            //
            //    $updatedColumns[$column][self::CSV_KEY] = $defs[$column];
            //
            //    // Taken from the ValidateShell
            //    $type = null;
            //    $limit = null;
            //    // Matches:
            //    // * date, time, string, and other simple types
            //    // * list(something), related(Others) and other simple limits
            //    // * related(Vendor/Plugin.Model) and other complex limits
            //    if (preg_match('/^(\w+?)\(([\w\/\.]+?)\)$/', $defs[$column]['type'], $matches)) {
            //        $type = $matches[1];
            //        $limit = $matches[2];
            //    } else {
            //        $type = $defs[$column]['type'];
            //    }
            //    $updatedColumns[$column][self::CSV_KEY]['type'] = $type;
            //    $updatedColumns[$column][self::CSV_KEY]['limit'] = $limit;
            //}
            //$result[$table] = $updatedColumns;
        }

        return $result;
    }


}
