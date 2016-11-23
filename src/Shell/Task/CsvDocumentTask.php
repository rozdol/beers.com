<?php
namespace App\Shell\Task;

use App\Shell\Task\SchemaTask;
use Cake\Console\Shell;
use Cake\I18n\Number;
use Cake\Utility\Inflector;

class CsvDocumentTask extends Shell
{
    public $tasks = [
        'CsvCommon',
        'Schema',
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
     * Main entry point
     *
     * @return void
     */
    public function main()
    {
    }

    /**
     * Create Markdown documentation for given table
     *
     * @param string $table Table name
     * @param array $properties Table properties
     * @param string $path Path to write the documentation file to
     * @param array $limitColumns Limit to only these columns
     * @return int Number of bytes written to the file
     */
    public function createMarkdown($table, array $properties, $path, array $limitColumns = [])
    {
        $result = 0;

        if (empty($table) || empty($properties) || empty($path)) {
            return $result;
        }
        $markdown = $this->buildMarkdown($table, $properties, $limitColumns);
        $result = file_put_contents($path, $markdown);
        if ($result === false) {
            throw new \RuntimeException("Failed to write to file: $path");
        }

        return $result;
    }

    /**
     * Build markdown
     *
     * @param string $table Table name
     * @param array $properties Table properties
     * @param array $limitColumns Limit to only these columns
     * @return string
     */
    protected function buildMarkdown($table, array $properties, array $limitColumns = [])
    {
        $result = '';

        // Header
        $result .= "Table: $table\n";
        $result .= "============================\n\n";
        $result .= "Generated on: " . date("Y-m-d H:i:s") . "\n\n";
        $result .= wordwrap("This file provides the description of fields for the table `$table`.\n\n");

        // Body
        $result .= "Columns\n";
        $result .= "-------\n\n";
        $columns = $this->mergeColumnDefinitions($properties);

        foreach ($columns as $column => $properties) {
            // If $limitColumns are defined and the column is not in there, skip
            if (!empty($limitColumns) && !in_array($column, $limitColumns)) {
                continue;
            }
            $result .= "### $column\n\n";
            foreach ($properties as $property => $value) {
                $result .= "* " . Inflector::humanize($property) . ": " . $value . "\n";
            }
            $result .= "\n";
        }

        return $result;
    }

    /**
     * Merge table schema and CSV definitions
     *
     * @param array $properties Table properties
     * @return array
     */
    protected function mergeColumnDefinitions(array $properties)
    {
        $result = [];

        // TODO : check if $properties['schema'] contains Cake\Database\Schema\Table instance

        $table = $properties[SchemaTask::KEY_SCHEMA]->name();
        $schema = [];
        $columns = $properties['schema']->columns();
        foreach ($columns as $column) {
            $schema[$column] = $properties[SchemaTask::KEY_SCHEMA]->column($column);
        }

        $csv = [];
        if (!empty($properties[SchemaTask::KEY_CSV_DEFS])) {
            $csv = $properties[SchemaTask::KEY_CSV_DEFS];
        }
        foreach ($columns as $column) {
            if (empty($csv[$column])) {
                // Populate with an empty item, so we can do processing later
                $csv[$column] = [
                    'name' => $column,
                    'type' => null,
                    'required' => null,
                    'non-searchable' => null,
                    'unique' => null
                ];
            }
        }

        // Use database schema as base, to avoid any extra leftovers from CSV
        foreach ($schema as $column => $properties) {
            $result[$column] = $properties;

            // Fix 'null'
            if ($csv[$column]['required'] || $csv[$column]['unique']) {
                $result[$column]['null'] = false;
            }

            // Fix 'comment'
            if (empty($result[$column]['comment'])) {
                $result[$column]['comment'] = $this->getDefaultColumnComment($table, $column);
            }

            // If 'comment' is still empty, stick table and field in there
            if (empty($result[$column]['comment'])) {
                $result[$column]['comment'] = Inflector::humanize(Inflector::singularize($table)) . ' ' . Inflector::humanize($column) . '.';
            }

            // Taken from the ValidateShell
            $type = null;
            $limit = null;
            // Matches:
            // * date, time, string, and other simple types
            // * list(something), related(Others) and other simple limits
            // * related(Vendor/Plugin.Model) and other complex limits
            if (preg_match('/^(\w+?)\(([\w\/\.]+?)\)$/', $csv[$column]['type'], $matches)) {
                $type = $matches[1];
                $limit = $matches[2];
            } else {
                $type = $csv[$column]['type'];
            }

            switch ($type) {
                case 'list':
                    $result[$column]['comment'] .= " Values from: `$limit` list.";
                    $result[$column]['length'] = 255;
                    $result[$column]['type'] = 'string';
                    $result[$column]['fixed'] = false;
                    break;
                case 'related':
                    $relatedTable = Inflector::tableize($limit);
                    $relatedField = 'id';
                    $related = $this->CsvCommon->mapTableField($relatedTable, $relatedField);
                    if (!$this->Schema->hasTableColumn($relatedTable, $related)) {
                        $this->err("Documentation for table $table.$column references non-existing field $relatedTable.$related");
                    }
                    $result[$column]['comment'] .= " Values from: `" . $relatedTable . "` table, `" . $related . "` field.";
                    $result[$column]['length'] = 255;
                    $result[$column]['type'] = 'string';
                    $result[$column]['fixed'] = false;
                    break;
            }

            // Fix 'comment' and 'length' for datetime, date, time, etc
            switch ($result[$column]['type']) {
                case 'uuid':
                    $result[$column]['comment'] .= " Format: https://en.wikipedia.org/wiki/Universally_unique_identifier .";
                    $result[$column]['length'] = 36;
                    $result[$column]['fixed'] = true;
                    break;
                case 'boolean':
                    $result[$column]['comment'] .= " Format: 1 for true, 0 for false.";
                    $result[$column]['length'] = 1;
                    $result[$column]['fixed'] = true;
                    break;
                case 'datetime':
                    $format = 'YYYY-MM-DD hh:mm:ss';
                    $result[$column]['comment'] .= " Format: $format.";
                    $result[$column]['length'] = strlen($format);
                    break;
                case 'date':
                    $format = 'YYYY-MM-DD';
                    $result[$column]['comment'] .= " Format: $format.";
                    $result[$column]['length'] = strlen($format);
                    break;
                case 'time':
                    $format = 'hh:mm:ss';
                    $result[$column]['comment'] .= " Format: $format.";
                    $result[$column]['length'] = strlen($format);
                    break;
            }

            // Rename 'null' for easier humanizing
            $result[$column]['null'] = $result[$column]['null'] ? 'yes' : 'no';
            $result[$column]['allow_null_values'] = $result[$column]['null'];
            unset($result[$column]['null']);

            // Rename 'default' for easier humanizing
            $result[$column]['default_value'] = $result[$column]['default'];
            unset($result[$column]['default']);

            // Rename 'fixed' for easier humanizing
            if (array_key_exists('fixed', $result[$column])) {
                $result[$column]['fixed'] = $result[$column]['fixed'] ? 'yes' : 'no';
                $result[$column]['fixed_length'] = $result[$column]['fixed'];
                unset($result[$column]['fixed']);
            }

            // Remove unnecessary 'precision'
            if (array_key_exists('precision', $result[$column]) && empty($result[$column]['precision'])) {
                unset($result[$column]['precision']);
            }

            // Remove 'collate' always
            if (array_key_exists('collate', $result[$column])) {
                unset($result[$column]['collate']);
            }

            // Remove unnecessary 'unsigned'
            if (array_key_exists('unsigned', $result[$column])) {
                if (empty($result[$column]['unsigned'])) {
                    unset($result[$column]['unsigned']);
                } else {
                    $result[$column]['unsigned'] = $result[$column]['unsigned'] ? 'yes' : 'no';
                }
            }

            // Make 'length' human friendly
            $result[$column]['length'] = Number::toReadableSize((int)$result[$column]['length']);
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
