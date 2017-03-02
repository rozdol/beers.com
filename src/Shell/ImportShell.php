<?php
namespace App\Shell;

use App\Shell\Task\SchemaTask;
use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Inflector;
use CsvMigrations\FieldHandlers\CsvField;

class ImportShell extends Shell
{
    public $tasks = [
        'CsvCommon',
        'CsvDocument',
        'CsvImport',
        'Schema',
    ];

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
        // DB Lists
        '/^dblists$/',
        '/^dblist_items$/',
        // Dashboards, saved searches, and widgets
        '/^dashboards.*$/',
        '/^saved_searches$/',
        '/^widgets$/',
        // Menus
        '/^menus$/',
        '/^menu_items$/',
        // Messages
        '/^messages$/',
        // Notes
        '/^notes$/',
        // Logs
        '/^database_logs$/',
        '/^log_audit$/',
        // CakeDC Users
        '/^social_accounts$/',
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
        if (empty($dest)) {
            $dest = $this->defaultDest;
        }

        if (!$this->CsvCommon->isWriteableDir($dest)) {
            $this->abort("Destination is not a writeable directory: $dest");
        }

        // Get all tables
        $tables = $this->Schema->getAllTables();

        // Order of filters is important
        $tables = $this->filterTablesByJoin($tables);
        $tables = $this->filterTablesByPattern($tables, $this->ignoreTablesPatterns);

        // Get database table schema and CSV definitions, if available
        $tableSchema = $this->Schema->getTableSchema($tables);
        $tableDefinitions = $this->Schema->getTableCsvDefinitions($tables);
        $tables = array_merge_recursive($tableSchema, $tableDefinitions);

        foreach ($tables as $table => $properties) {
            $columns = $properties[SchemaTask::KEY_SCHEMA]->columns();
            $columns = $this->filterColumns($table, $columns);

            // Create CSV template
            $csvPath = $dest . DIRECTORY_SEPARATOR . $this->csvDir;
            if (!file_exists($csvPath)) {
                if (!mkdir($csvPath)) {
                    $this->abort("Failed to create path: $csvPath");
                }
            }
            $csvPath .= DIRECTORY_SEPARATOR . $table . '.csv';
            $csvBytes = 0;
            try {
                $csvBytes = $this->CsvImport->createTemplate($columns, $csvPath);
            } catch (\Exception $e) {
                $this->abort($e->getMessage());
            }
            $this->out("Created $csvPath ($csvBytes bytes)");

            // If there is no template, no need for documentation either
            if (!$csvBytes) {
                continue;
            }

            // Create Markdown documentation
            $docPath = $dest . DIRECTORY_SEPARATOR . $this->docDir;
            if (!file_exists($docPath)) {
                if (!mkdir($docPath)) {
                    $this->abort("Failed to create path: $docPath");
                }
            }
            $docPath .= DIRECTORY_SEPARATOR . $table . '.md';
            $docBytes = 0;
            try {
                $docBytes = $this->CsvDocument->createMarkdown($table, $properties, $docPath, $columns);
            } catch (\Exception $e) {
                $this->abort($e->getMessage());
            }
            $this->out("Created $docPath ($docBytes bytes)");
        }
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
     * Filter out columns that don't need documentation
     *
     * @param string $table Table name
     * @param array $columns Column definitions
     * @return array
     */
    protected function filterColumns($table, array $columns)
    {
        $result = [];

        $ignoreTableColumns = $this->CsvCommon->getIgnoreTableColumns();
        foreach ($columns as $column => $properties) {
            // Skip column if it is in the table ignore columns list
            if (!empty($ignoreTableColumns[$table])) {
                if (in_array($column, $ignoreTableColumns[$table])) {
                    continue;
                }
            }
            // Skip column if it is in the all ignore columns list
            if (!empty($ignoreTableColumns['*'])) {
                if (in_array($column, $ignoreTableColumns['*'])) {
                    continue;
                }
            }
            $result[$column] = $properties;
        }

        return $result;
    }
}
