<?php
namespace App\SystemInfo;

use Cake\Core\Configure;
use Cake\Core\Plugin;

/**
 * Cake class
 *
 * This is a helper class that assists with
 * fetching a variety of CakePHP information
 * from the system.
 */
class Cake
{
    /**
     * Get CakePHP version
     *
     * @return string
     */
    public static function getVersion()
    {
        return Configure::version();
    }

    /**
     * Get the list of loaded CakePHP plugins
     *
     * @return array
     */
    public static function getLoadedPlugins()
    {
        return Plugin::loaded();
    }
}
