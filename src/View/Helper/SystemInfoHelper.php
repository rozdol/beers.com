<?php
namespace App\View\Helper;

use App\SystemInfo\Cake;
use App\SystemInfo\Composer;
use App\SystemInfo\Database;
use App\SystemInfo\Git;
use App\SystemInfo\Project;
use App\SystemInfo\Server;

use Cake\View\Helper;

/**
 * SystemInfoHelper class
 */
class SystemInfoHelper extends Helper
{
    /**
     *  getProjectVersion method
     *
     * @return string project version
     */
    public function getProjectVersion()
    {
        return Project::getDisplayVersion();
    }

    /**
     * getProjectVersions method
     *
     * @return array with project versions
     */
    public function getProjectVersions()
    {
        return Project::getBuildVersions();
    }

    /**
     * getProjectUrl method
     *
     * @return string project's URL
     */
    public function getProjectUrl()
    {
        return Project::getUrl();
    }

    /**
     * getProjectName method
     *
     * @return string project name
     */
    public function getProjectName()
    {
        return Project::getName();
    }

    /**
     * getTableStats method
     *
     * @return array with table stats
     */
    public function getTableStats()
    {
        return Database::getTableStats();
    }

    /**
     * getAllTables method
     *
     * @return array of all tables in the database
     */
    public function getAllTables()
    {
        return Database::getAllTables();
    }

    /**
     * getProgressValue method
     *
     * @param int $progress value
     * @param int $total value
     * @return int progress result
     */
    public function getProgressValue($progress, $total)
    {
        $result = '0%';

        if (!$progress || !$total) {
            return $result;
        }

        $result = number_format(100 * $progress / $total, 0) . '%';

        return $result;
    }

    /**
     * getLocalModifications method
     *
     * @return array with local modifications
     */
    public function getLocalModifications()
    {
        return Git::getLocalChanges();
    }

    /**
     * getLocalModificationsCommand method
     *
     * @return string local modification command
     */
    public function getLocalModificationsCommand()
    {
        return Git::getCommand('localChanges');
    }

    /**
     *  getServerInfo method
     *
     * @return array with server details
     */
    public function getServerInfo()
    {
        return Server::getInfo();
    }

    /**
     * getNumberOfCpus method
     *
     * @return int number of CPUs
     */
    public function getNumberOfCpus()
    {
        return Server::getNumberOfCpus();
    }

    /**
     * getTotalRam method
     *
     * @return string total RAM value
     */
    public function getTotalRam()
    {
        return Server::getTotalRam();
    }

    /**
     *  getCakePhpPlugins method
     *
     * @return array of CakePHP plugins
     */
    public function getCakePhpPlugins()
    {
        return Cake::getLoadedPlugins();
    }

    /**
     * getCakePhpVersion method
     *
     * @return string CakePHP version
     */
    public function getCakePhpVersion()
    {
        return Cake::getVersion();
    }

    /**
     *  getComposerPackages method
     *
     * @return array of installed composer packages
     */
    public function getComposerPackages()
    {
        return Composer::getInstalledPackages();
    }

    /**
     *  getComposerMatchCounts method
     *
     * @param array $packages installed composer packages
     * @param array $matchWords list of words to match
     * @return array packages matched specified words
     */
    public function getComposerMatchCounts(array $packages, array $matchWords)
    {
        return Composer::getMatchCounts($packages, $matchWords);
    }

    /**
     *  getProjectLogo method
     *
     * @param string $logoSize of logo - mini or large
     * @return string base64 encoded project logo
     */
    public function getProjectLogo($logoSize = '')
    {
        return Project::getLogo($logoSize);
    }

    /**
     * getCopyright method
     *
     * @return string copyright
     */
    public function getCopyright()
    {
        return Project::getCopyright();
    }
}
