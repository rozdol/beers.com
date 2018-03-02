<?php
namespace App\SystemInfo;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use ReflectionClass;

/**
 * Database class
 *
 * This is a helper class that assists with
 * fetching a variety of database information
 * from the system.
 */
class Database
{
    /**
     * Get driver and version
     *
     * Get the database driver in use, and, if possible,
     * the version of the database engine
     *
     * @param bool $skipVersion Do not include the version
     * @return string
     */
    public static function getDriver($skipVersion = false)
    {
        $driver = ConnectionManager::get('default')->driver();
        // Find the class name of the driver without namespace
        $driver = new ReflectionClass($driver);
        $driver = $driver->getShortName();
        $driver = strtoupper($driver);

        if ($skipVersion) {
            return $driver;
        }

        // Find version of the database engine
        switch ($driver) {
            case 'MYSQL':
                $version = ConnectionManager::get('default')->execute("SELECT VERSION()");
                $version = $version->fetch()[0];
                $driver .= ' ' . $version;

                break;
        }

        return $driver;
    }

    /**
     * Get all tables
     *
     * This method returns a list of all tables found
     * in the default database connection.
     *
     * @return array
     */
    public static function getAllTables()
    {
        return ConnectionManager::get('default')->schemaCollection()->listTables();
    }

    /**
     * Get table stats
     *
     * This method returns a variety of stats, such as
     * the count of all records, deleted records, etc.
     *
     * @return array
     */
    public static function getTableStats()
    {
        //
        // Statistics
        //
        $allTables = static::getAllTables();
        $skipTables = 0;
        $tableStats = [];
        foreach ($allTables as $table) {
            // Skip phinx database schema version tables
            if (preg_match('/phinxlog/', $table)) {
                $skipTables++;
                continue;
            }
            // Bypassing any CakePHP logic for permissions, pagination, and so on,
            // and executing raw query to get reliable data.
            $sth = ConnectionManager::get('default')->execute("SELECT COUNT(*) AS total FROM `$table`");
            $result = $sth->fetch('assoc');
            $tableStats[$table]['total'] = $result['total'];

            $tableInstance = TableRegistry::get($table);
            $tableStats[$table]['deleted'] = 0;
            if ($tableInstance->hasField('trashed')) {
                $sth = ConnectionManager::get('default')->execute("SELECT COUNT(*) AS deleted FROM `$table` WHERE `trashed` IS NOT NULL AND `trashed` <> '0000-00-00 00:00:00'");
                $result = $sth->fetch('assoc');
                $tableStats[$table]['deleted'] = $result['deleted'];
            }
        }

        return [$skipTables, $tableStats];
    }
}
