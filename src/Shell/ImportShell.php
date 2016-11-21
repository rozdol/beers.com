<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Inflector;

class ImportShell extends Shell
{
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
     * List of columns to ignore in documentation
     *
     * Associative array of $table => $columns to ignore
     * in generated documentation.  A special table name
     * '*' can be used to ignore column in all tables.
     *
     * @var array $ignoreColumnsDocs List of table columns to ignore in documentation
     */
    protected $ignoreColumnsDocs = [
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
                $result .= "* " . Inflector::humanize($property) . ": $value\n";
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
            if (!empty($this->ignoreColumnsDocs[$table])) {
                if (in_array($column, $this->ignoreColumnsDocs[$table])) {
                    continue;
                }
            }
            // Skip column if it is in the all ignore columns list
            if (!empty($this->ignoreColumnsDocs['*'])) {
                if (in_array($column, $this->ignoreColumnsDocs['*'])) {
                    continue;
                }
            }
            $result[$column] = $properties;
        }

        return $result;
    }
}
