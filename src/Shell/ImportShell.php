<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;

class ImportShell extends Shell
{
    /**
     * Default folder to write files to
     *
     * @var string $defaultDest Path to folder
     */
    protected $defaultDest = '/tmp';

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

    public function main()
    {
        $this->out("Usage: cake import create_templates /tmp/folder");
    }

    public function createTemplates($dest = null)
    {
        if (empty($dest)) {
            $dest = $this->defaultDest;
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
        $result = $this->createCsvTemplates($tables, $dest);
        if (empty($result)) {
            throw new \RuntimeException("No CSV templates were created");
        }
        foreach ($result as $table => $file) {
            $this->out("Template for table '$table' created in '$file'.");
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
            $result[$table]  = ConnectionManager::get('default')->schemaCollection()->describe($table);
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
    protected function createCsvTemplates(array $tables, $dest = null)
    {
        if (empty($tables)) {
            return $tables;
        }

        if (empty($dest)) {
            $dest = $this->defaultDest;
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
}
