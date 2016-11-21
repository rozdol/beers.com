<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use CsvMigrations\FieldHandlers\CsvField;

class ImportShell extends Shell
{
    /**
     * CSV definitions array key
     */
    const CSV_KEY = '_csv';

    /**
     * Current timestamp to share between files
     *
     * @var string $timestamp Timestamp
     */
    protected $timeStamp;

    /**
     * Default folder to write files to
     *
     * @var string $defaultDest Path to folder
     */
    protected $defaultDest = '/tmp';

    /**
     * Sub-directory to save CSV files to
     *
     * @var string $csvDir CSV sub-directory
     */
    protected $csvDir = 'csv';

    /**
     * Sub-directory to save DOC files to
     *
     * @var string $docDir DOC sub-directory
     */
    protected $docDir = 'doc';

    /**
     * Patterns for table names to ignore
     *
     * @var array $ignoreTablesPatterns List of regular expressions
     */
    protected $ignoreTablesPatterns = [
        // Phinx database schema versioning
        '/phinxlog/',
        // File uploads and meta information
        '/^files$/',
        '/^file_storage$/',
        // Roles and capabilities
        '/^capabilities$/',
        '/^roles$/',
        '/^groups_roles$/',
        // Dashboards, saved searches, and widgets
        '/^dashboards.*$/',
        '/^saved_searches$/',
        '/^widgets$/',
        // Menus
        '/^menus$/',
        '/^menu_items$/',
        // Messages
        '/^messages$/',
        // Logs
        '/^database_logs$/',
        '/^log_audit$/',
        // CakeDC Users
        '/^social_accounts$/',
    ];

    /**
     * List of columns to ignore
     *
     * Associative array of $table => $columns to ignore.
     * A special table name '*' can be used to ignore
     * column in all tables.
     *
     * @var array $ignoreTableColumns List of table columns to ignore
     */
    protected $ignoreTableColumns = [
        '*' => [
            'id',
        ],
        'users' => [
            'token',
            'token_expires',
            'api_token',
            'activation_date',
            'tos_date',
            'role',
        ],
    ];

    /**
     * List of default columns comments
     *
     * If the database schema does not provide a column
     * comment, the one from this list will be used.
     *
     * A special table name '*' can be used to set the
     * default comment for the column in all tables.
     *
     * @var array $defaultColumnComments List of table columns to ignore
     */
    protected $defaulColumnComments = [
        '*' => [
            'created' => 'Date and time the record was created.',
            'modified' => 'Date and time the record was last modified.',
            'trashed' => 'Date and time the record was deleted.',
        ],
    ];

    /**
     * Shell entry point
     *
     * @return void
     */
    public function main()
    {
        $this->out("Usage: cake import create_templates /tmp/folder");
    }

    /**
     * Create CSV templates and documentation
     *
     * @param string $dest Path to destination folder (must exist and be writeable)
     * @return void
     */
    public function createTemplates($dest = null)
    {
        // Set common time stamp
        $this->timeStamp = date('Y-m-d H:i:s');

        if (empty($dest)) {
            $dest = $this->defaultDest;
        }

        try {
            $this->validateDir($dest);
        } catch (\Exception $e) {
            $this->abort("Directory is not valid: " . $e->getMessage());
        }

        // Get all tables
        $tables = $this->getAllTables();

        // Order of filters is important
        $tables = $this->filterTablesByJoin($tables);
        $tables = $this->filterTablesByPattern($tables, $this->ignoreTablesPatterns);

        // Get table schema
        $tables = $this->getTableSchema($tables);
        $tables = $this->getTableColumns($tables);
        $tables = $this->getTableCsvDefinitions($tables);

        // Create CSV templates
        $csvResult = $this->createCsvTemplates($tables, $dest);
        if (empty($csvResult)) {
            $this->abort("No CSV templates were create");
        }

        $result = [];
        foreach ($csvResult as $table => $file) {
            $result[$table][] = $file;
        }

        // Create DOC files
        $docResult = $this->createCsvDocuments($tables, $dest);
        if (!empty($docResult)) {
            foreach ($docResult as $table => $file) {
                $result[$table][] = $file;
            }
        }

        $this->out("The following files were created:\n");
        foreach ($result as $table => $files) {
            $this->out("For table $table:");
            foreach ($files as $file) {
                $this->out("\t$file");
            }
        }
    }

    /**
     * Validate directory
     *
     * Directory has to exist and has to be writeable
     *
     * @throws InvalidArgumentException When directory is not valid
     * @param string $dir Destination directory to check
     * @return void
     */
    protected function validateDir($dir)
    {
        $dir = (string)$dir;
        if (empty($dir)) {
            throw new \InvalidArgumentException("Destination directory is not specified");
        }
        if (!file_exists($dir)) {
            throw new \InvalidArgumentException("Destination directory does not exist");
        }
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException("Destination is not a directory");
        }
        if (!is_writeable($dir)) {
            throw new \InvalidArgumentException("Destination directory is not writeable");
        }
    }


    /**
     * Get all database tables
     *
     * @return array
     */
    protected function getAllTables()
    {
        $result = ConnectionManager::get('default')->schemaCollection()->listTables();

        return $result;
    }

    /**
     * Filter out tables that match any of the given patterns
     *
     * @param array $tables List of tables to process
     * @param array $patterns List of patterns to match against
     * @return array
     */
    protected function filterTablesByPattern(array $tables, array $patterns = [])
    {
        // If no tables or patterns, return the list as is
        if (empty($tables) || empty($patterns)) {
            return $tables;
        }

        $result = [];
        foreach ($tables as $table) {
            $matched = false;
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $table)) {
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                $result[] = $table;
            }
        }

        return $result;
    }

    /**
     * Filter out join tables
     *
     * Join tables follow the CakePHP naming convention,
     * where the names of both tables are concatenated
     * in alphabetical order, using underscore (_) as a
     * separatator.
     *
     * @param array $tables List of tables
     * @return array
     */
    protected function filterTablesByJoin(array $tables)
    {
        // If no tables are given, we have nothing to do
        if (empty($tables)) {
            return $tables;
        }

        $result = [];

        foreach ($tables as $table) {
            // If table name doesn't have underscore, it's not a join table
            if (!preg_match('/_/', $table)) {
                $result[] = $table;
                continue;
            }

            // Examine each part of the table name
            // FIXME: This breaks on joins like 'dashboards'_'saved_searches'.
            $parts = explode('_', $table);
            $partTableCounter = 0;
            foreach ($parts as $part) {
                // If part found in the list of tables, increment counter
                if (in_array($part, $tables)) {
                    $partTableCounter++;
                }
            }

            // It's a join table only if all parts are found in the table list
            if ($partTableCounter < count($parts)) {
                $result[] = $table;
            }
        }

        return $result;
    }

    /**
     * Get schema definitions for each table
     *
     * @param array $tables List of tables
     * @return array
     */
    protected function getTableSchema(array $tables)
    {
        if (empty($tables)) {
            return $tables;
        }

        $result = [];
        foreach ($tables as $table) {
            $result[$table] = ConnectionManager::get('default')->schemaCollection()->describe($table);
        }

        return $result;
    }

    /**
     * Get detail information about table columns
     *
     * @param array $tables List of tables
     * @return array
     */
    protected function getTableColumns(array $tables)
    {
        if (empty($tables)) {
            return $tables;
        }

        $result = [];
        foreach ($tables as $table => $schema) {
            $columns = $schema->columns();
            $result[$table] = [];
            foreach ($columns as $column) {
                $result[$table][$column] = $schema->column($column);
            }
        }

        return $result;
    }

    /**
     * Get CSV field definitions for given tables
     *
     * @param array $tables List of tables
     * @return array
     */
    protected function getTableCsvDefinitions(array $tables)
    {
        if (empty($tables)) {
            return $tables;
        }

        $result = [];
        foreach ($tables as $table => $columns) {
            $tableName = Inflector::camelize($table);
            $defs = [];
            try {
                $tableObject = TableRegistry::get($tableName);
                $defs = $tableObject->getFieldsDefinitions($tableName);
            } catch (\Exception $e) {
                // Skip non-CSV based table
            }
            // If no field definitions from CSV, then move on
            if (empty($defs)) {
                $result[$table] = $columns;
                continue;
            }
            $updatedColumns = [];
            foreach ($columns as $column => $properties) {
                $updatedColumns[$column] = $properties;
                if (!in_array($column, array_keys($defs))) {
                    continue;
                }

                $updatedColumns[$column][self::CSV_KEY] = $defs[$column];

                // Taken from the ValidateShell
                $type = null;
                $limit = null;
                // Matches:
                // * date, time, string, and other simple types
                // * list(something), related(Others) and other simple limits
                // * related(Vendor/Plugin.Model) and other complex limits
                if (preg_match('/^(\w+?)\(([\w\/\.]+?)\)$/', $defs[$column]['type'], $matches)) {
                    $type = $matches[1];
                    $limit = $matches[2];
                } else {
                    $type = $defs[$column]['type'];
                }
                $updatedColumns[$column][self::CSV_KEY]['type'] = $type;
                $updatedColumns[$column][self::CSV_KEY]['limit'] = $limit;
            }
            $result[$table] = $updatedColumns;
        }

        return $result;
    }

    /**
     * Create CSV templates
     *
     * @throws RuntimeException When cannot write files
     * @param array $tables List of tables
     * @param string $dest Destination folder
     * @return array List of tables and files written
     */
    protected function createCsvTemplates(array $tables, $dest)
    {
        if (empty($tables)) {
            return $tables;
        }

        $dest = $dest . DIRECTORY_SEPARATOR . $this->csvDir;
        if (!file_exists($dest)) {
            $mkdirResult = mkdir($dest);
            if (!$mkdirResult) {
                throw new \RuntimeException("Failed to create CSV folder [$dest]");
            }
        }

        $result = [];
        foreach ($tables as $table => $columns) {
            $columns = $this->filterColumns($table, $columns);
            if (empty($columns)) {
                continue;
            }

            $csvFilePath = $dest . DIRECTORY_SEPARATOR . $table . '.csv';
            $fh = fopen($csvFilePath, 'w');
            if (!is_resource($fh)) {
                throw new \RuntimeException("Failed to open CSV file for writing: $csvFilePath");
            }
            $csvBytes = fputcsv($fh, array_keys($columns));
            if (!$csvBytes) {
                fclose($fh);
                throw new \RuntimeException("Failed to write to CSV file: $csvFilePath");
            }
            fclose($fh);
            $result[$table] = $csvFilePath;
        }

        return $result;
    }

    /**
     * Create CSV documentation
     *
     * @throws RuntimeException When cannot write files
     * @param array $tables List of tables
     * @param string $dest Destination folder
     * @return array List of tables and files written
     */
    protected function createCsvDocuments(array $tables, $dest)
    {
        if (empty($tables)) {
            return $tables;
        }

        $dest = $dest . DIRECTORY_SEPARATOR . $this->docDir;
        if (!file_exists($dest)) {
            $mkdirResult = mkdir($dest);
            if (!$mkdirResult) {
                throw new \RuntimeException("Failed to create DOC folder [$dest]");
            }
        }

        $result = [];
        foreach ($tables as $table => $columns) {
            $markdown = $this->createTableMarkdown($table, $columns);
            if (empty($markdown)) {
                continue;
            }

            $docFilePath = $dest . DIRECTORY_SEPARATOR . $table . '.md';
            $fh = fopen($docFilePath, 'w');
            if (!is_resource($fh)) {
                throw new \RuntimeException("Failed to open DOC file for writing: $docFilePath");
            }
            $docBytes = fwrite($fh, $markdown);
            if (!$docBytes) {
                fclose($fh);
                throw new \RuntimeException("Failed to write to DOC file: $docFilePath");
            }
            fclose($fh);
            $result[$table] = $docFilePath;
        }

        return $result;
    }

    /**
     * Create Markdown document for a given table scheme
     *
     * @param string $table Table name
     * @param array $columns Table columns
     * @return string Markdown document
     */
    protected function createTableMarkdown($table, array $columns)
    {
        $result = '';

        $columns = $this->filterColumns($table, $columns);
        if (empty($columns)) {
            return $result;
        }

        $result .= "Table: $table\n";
        $result .= "============================\n";
        $result .= "\n";
        $result .= "Generated on: " . $this->timeStamp . "\n";
        $result .= "\n";
        $result .= wordwrap("This file provides the description of fields for import into the table `$table`.\n");
        $result .= "\n";
        $result .= "Columns\n";
        $result .= "-------\n";
        $result .= "\n";
        foreach ($columns as $name => $properties) {
            $result .= "### $name\n";
            $result .= "\n";
            foreach ($properties as $property => $value) {
                if ($property == self::CSV_KEY) {
                    continue;
                }

                $extra = '';
                switch ($property) {
                    case 'comment':
                        if (empty($value)) {
                            $value = $this->getDefaultColumnComment($table, $name);
                        }
                        break;
                    case 'null':
                        $property = 'allow_null_values';
                        // If DB doesn't require, but CSV does, then we require
                        if (!$value && !empty($properties[self::CSV_KEY]) && $properties[self::CSV_KEY]['required']) {
                            $value = true;
                        }
                        $value = $value ? 'yes' : 'no';
                        break;
                    case 'default':
                        $property = 'default_value';
                        break;
                    case 'type':
                        switch ($value) {
                            case 'uuid':
                                if (!empty($properties[self::CSV_KEY]) && $properties[self::CSV_KEY]['type'] == 'related') {
                                    $value = 'string';
                                    $extra = "* References to: `" . Inflector::tableize($properties[self::CSV_KEY]['limit']) . "` table.\n";
                                } else {
                                    $extra .= "* Format: [36 character long UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier)\n";
                                }
                                break;
                            case 'string':
                                debug($properties);
                                if (!empty($properties[self::CSV_KEY]) && $properties[self::CSV_KEY]['type'] == 'list') {
                                    $extra = "* References to: `" . $properties[self::CSV_KEY]['limit'] . "` list.\n";
                                }
                                break;
                            case 'time':
                                $extra .= "* Format: hh:mm:ss\n";
                                break;
                            case 'date':
                                $extra .= "* Format: YYYY-MM-DD\n";
                                break;
                            case 'datetime':
                                $extra .= "* Format: YYYY-MM-DD hh:mm:ss\n";
                                break;
                        }
                        break;
                }

                $result .= "* " . Inflector::humanize($property) . ": $value\n";
                $result .= $extra;
            }
            $result .= "\n";
        }

        return $result;
    }

    /**
     * Filter out columns that don't need documentation
     *
     * @param string $table Table name
     * @param array $columns Column definitions
     * @return array
     */
    protected function filterColumns($table, array $columns)
    {
        $result = [];

        foreach ($columns as $column => $properties) {
            // Skip column if it is in the table ignore columns list
            if (!empty($this->ignoreTableColumns[$table])) {
                if (in_array($column, $this->ignoreTableColumns[$table])) {
                    continue;
                }
            }
            // Skip column if it is in the all ignore columns list
            if (!empty($this->ignoreTableColumns['*'])) {
                if (in_array($column, $this->ignoreTableColumns['*'])) {
                    continue;
                }
            }
            $result[$column] = $properties;
        }

        return $result;
    }

    /**
     * Get default column comment
     *
     * @param string $table Table name
     * @param string $column Column name
     * @return string
     */
    protected function getDefaultColumnComment($table, $column)
    {
        $result = '';

        if (!empty($this->defaulColumnComments[$table])
            && in_array($column, array_keys($this->defaulColumnComments[$table]))) {
            $result = $this->defaulColumnComments[$table][$column];
        } elseif (!empty($this->defaulColumnComments['*'])
            && in_array($column, array_keys($this->defaulColumnComments['*']))) {
            $result = $this->defaulColumnComments['*'][$column];
        }

        return $result;
    }
}
