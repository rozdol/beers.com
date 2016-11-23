<?php
namespace App\Shell\Task;

use Cake\Console\Shell;

class CsvCommonTask extends Shell
{
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
     * Map of ID fields for tables
     *
     * This list provides a list of tables,
     * mapping internal field names to
     * external field names.
     *
     * A special table name '*' can be used
     * to specify the defaults for all tables.
     *
     * @var array $tableColumnMap Table column map
     */
    protected $tableColumnMap = [
        '*' => [
            'id' => 'legacy_id',
        ],
        'users' => [
            'id' => 'username',
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
     * Get ignore table columns
     *
     * @return array
     */
    public function getIgnoreTableColumns()
    {
        return $this->ignoreTableColumns;
    }

    /**
     * Get table column map
     *
     * @return array
     */
    public function getTableColumnMap()
    {
        return $this->tableColumnMap;
    }

    /**
     * Map table column name
     *
     * Figure out the name of the column,
     * for a given column of a given table.
     *
     * @param string $table Table name
     * @param string $column Column name
     * @return string
     */
    public function mapTableField($table, $column)
    {
        // If no column map found, return as is
        $result = $column;

        $tableColumnMap = $this->getTableColumnMap();
        if (!empty($tableColumnMap[$table][$column])) {
            $result = $tableColumnMap[$table][$column];
        } elseif (!empty($tableColumnMap['*'][$column])) {
            $result = $tableColumnMap['*'][$column];
        } else {
        }

        return $result;
    }

    /**
     * Check if given path is a writeable directory
     *
     * @param string $dir Destination directory to check
     * @return boolean True on yes, false otherwise.
     */
    public function isWriteableDir($dir)
    {
        $result = false;

        $dir = (string)$dir;
        if (empty($dir)) {
            return $result;
        }
        if (!file_exists($dir)) {
            return $result;
        }
        if (!is_dir($dir)) {
            return $result;
        }
        if (!is_writeable($dir)) {
            return $result;
        }

        $result = true;

        return $result;
    }

}
