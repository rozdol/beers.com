<?php
namespace App\Shell\Task;

use Cake\Console\Shell;
use Cake\I18n\Number;
use Cake\Utility\Inflector;

class CsvDocumentTask extends Shell
{
    public $tasks = [
        'CsvCommon',
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

    public function main()
    {
    }

    /**
     * Create Markdown documentation for given table
     *
     * @param string $table Table name
     * @param array $properties Table properties
     * @param string $path Path to write the documentation file to
     * @return int Number of bytes written to the file
     */
    public function createMarkdown($table, array $properties, $path)
    {
        $result = 0;

        if (empty($table) || empty($properties) || empty($path)) {
            return $result;
        }
        $markdown = $this->buildMarkdown($table, $properties);
        $result = file_put_contents($path, $markdown);
        if ($result === false) {
            throw new \RuntimeException("Failed to write to file: $path");
        }

        return $result;
    }

    protected function buildMarkdown($table, $properties)
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
            $result .= "### $column\n\n";
            foreach ($properties as $property => $value) {
                $result .= "* " . Inflector::humanize($property) . ": " . $value . "\n";
            }
            $result .= "\n";
        }

        return $result;
    }

    protected function mergeColumnDefinitions(array $properties)
    {
        $result = [];

        // TODO : check if $properties['schema'] contains Cake\Database\Schema\Table instance

        $table = $properties['schema']->name();
        $schema = [];
        $columns = $properties['schema']->columns();
        foreach ($columns as $column) {
            $schema[$column] = $properties['schema']->column($column);
        }

        $csv = [];
        if (!empty($properties['csvDefinitions'])) {
            $csv = $properties['csvDefinitions'];
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
            $result[$column]['null'] = $result[$column]['null'] ? 'yes' : 'no';

            // Fix 'comment'
            if (empty($result[$column]['comment'])) {
                $result[$column]['comment'] = $this->getDefaultColumnComment($table, $column);
            }

            // Fix 'comment' and 'length' for datetime, date, and time
            switch($result[$column]['type']) {
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
                    break;
                case 'related':
                    $relatedTable = Inflector::tableize($limit);
                    $relatedField = 'id';
                    $related = $this->CsvCommon->mapTableField($relatedTable, $relatedField);
                    $result[$column]['comment'] .= " Values from: `" . $relatedTable . "` table, `" . $related . "` field.";
                    $result[$column]['length'] = 255;
                    $result[$column]['type'] = 'string';
                    break;
            }

            // Rename 'null' for easier humanizing
            $result[$column]['allow_null_values'] = $result[$column]['null'];
            unset($result[$column]['null']);

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
