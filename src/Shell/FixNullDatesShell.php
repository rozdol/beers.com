<?php

namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;

/**
 *  Set value of the datetime field in case it allows null and it has value 0000-00-00 00:00:0
 */
class FixNullDatesShell extends Shell
{
    /**
     *  Default datetime field name
     */
    const DATETIME_FIELD = 'trashed';

    /**
     *  Run update process
     */
    public function main()
    {
        $this->out("Starting ...");

        $field = !empty($this->args[0]) ? $this->args[0] : self::DATETIME_FIELD;

        $db = ConnectionManager::get('default');
        $collection = $db->schemaCollection();
        $tables = $collection->listTables();

        $this->out('List of tables: ' . print_r($tables, true));

        foreach ($tables as $tbl) {
            $tblSchema = $collection->describe($tbl);

            $columns = $tblSchema->columns();

            if (in_array($field, $columns)) {
                if ($tblSchema->isNullable($field)) {
                    $this->out("Table: $tbl :: $field can be null. Update data ...");
                    $result = $db->query("UPDATE $tbl SET $field=NULL WHERE CAST($field as CHAR(20)) = '0000-00-00 00:00:00'");
                } else {
                    $this->out("Table: $tbl :: $field cannot be null!");
                }
            }
        }
    }

    /**
     * Configure option parser
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->description('Get datetime field');
        $parser->addArgument('target_field', [
            'help' => 'Target field to fix datetime null value (required)',
            'required' => true,
        ]);

        return $parser;
    }
}
