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
     * @var string $releasesUrl Base URL to CakePHP releases
     */
    protected static $releasesUrl = 'https://github.com/cakephp/cakephp/releases/tag/';

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
     * Get CakePHP version URL
     *
     * This method returns the URL to the release
     * notes of a given version.  If the version
     * is not specified, then the URL to the current
     * CakePHP version will be returned.
     *
     * @param string $version CakePHP version
     * @return string
     */
    public static function getVersionUrl($version = null)
    {
        if (empty($version)) {
            $version = static::getVersion();
        }

        return static::$releasesUrl . $version;
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
