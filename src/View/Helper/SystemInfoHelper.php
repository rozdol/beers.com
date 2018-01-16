<?php

namespace App\View\Helper;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\View\Helper;

/**
 * SystemInfoHelper class
 */
class SystemInfoHelper extends Helper
{
    /**
     * @var $logoSizes
     */
    protected $logoSizes = ['mini', 'large'];

    /**
     * @var $defaultLogoSize
     */
    protected $defaultLogoSize = 'mini';

    /**
     *  getProjectVersion method
     *
     * @return string project version
     */
    public function getProjectVersion()
    {
        // Use PROJECT_VERSION environment variable or fallback
        $projectVersion = getenv('PROJECT_VERSION') ?: getenv('GIT_BRANCH');
        $lastCommit = shell_exec('git rev-parse --short HEAD');
        $lastCommit = $lastCommit ?: 'N/A';
        $projectVersion = $projectVersion ?: $lastCommit;

        return $projectVersion;
    }

    /**
     * getProjectVersions method
     *
     * @return array with project versions
     */
    public function getProjectVersions()
    {
        // Read build/version* files or use N/A as fallback
        $versions = [
            'current' => ROOT . DS . 'build' . DS . 'version',
            'deployed' => ROOT . DS . 'build' . DS . 'version.ok',
            'previous' => ROOT . DS . 'build' . DS . 'version.bak',
        ];
        foreach ($versions as $version => $file) {
            if (is_readable($file)) {
                $versions[$version] = file_get_contents($file);
            } else {
                $versions[$version] = 'N/A';
            }
        }

        return $versions;
    }

    /**
     * getProjectUrl method
     *
     * @return string project's URL
     */
    public function getProjectUrl()
    {
        // Use PROJECT_URL environment variable or fallback URL
        $projectUrl = getenv('PROJECT_URL');
        $projectUrl = $projectUrl ?: \Cake\Routing\Router::fullBaseUrl();
        $projectUrl = $projectUrl ?: 'https://github.com/QoboLtd/project-template-cakephp';

        return $projectUrl;
    }

    /**
     * getProjectName method
     *
     * @return string project name
     */
    public function getProjectName()
    {
        // Use PROJECT_NAME environment variable or project folder name
        $projectName = getenv('PROJECT_NAME') ?: basename(ROOT);

        return $projectName;
    }

    /**
     * getTableStats method
     *
     * @return array with table stats
     */
    public function getTableStats()
    {
        //
        // Statistics
        //
        $allTables = $this->getAllTables();
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

    /**
     * getAllTables method
     *
     * @return array of all tables in the database
     */
    public function getAllTables()
    {
        $allTables = ConnectionManager::get('default')->schemaCollection()->listTables();

        return $allTables;
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
        $localModificationsCommand = $this->getLocalModificationsCommand();

        $localModifications = trim(shell_exec($localModificationsCommand));
        $localModifications = empty($localModifications) ? [] : explode("\n", $localModifications);

        return $localModifications;
    }

    /**
     * getLocalModificationsCommand method
     *
     * @return string local modification command
     */
    public function getLocalModificationsCommand()
    {
        $localModificationsCommand = 'git status --porcelain';

        return $localModificationsCommand;
    }

    /**
     *  getServerInfo method
     *
     * @return array with server details
     */
    public function getServerInfo()
    {
        $server = [
            'Hostname' => gethostname(),
            'Operating System' => implode(' ', [
                php_uname('s'),
                php_uname('r'),
            ]),
            'Machine Type' => php_uname('m'),
            'Number of CPUs' => $this->getNumberOfCpus(),
            'Total RAM' => $this->getTotalRam(),
        ];

        return $server;
    }

    /**
     * getNumberOfCpus method
     *
     * @return int number of CPUs
     */
    public function getNumberOfCpus()
    {
        $numCpus = 0;
        $cpuInfoFile = '/proc/cpuinfo';
        if (is_file($cpuInfoFile) && is_readable($cpuInfoFile)) {
            $cpuInfoFile = file($cpuInfoFile);
            $cpus = preg_grep("/^processor/", $cpuInfoFile);
            $numCpus = count($cpus);
        }

        return $numCpus;
    }

    /**
     * getTotalRam method
     *
     * @return string total RAM value
     */
    public function getTotalRam()
    {
        $totalRam = 'N/A';
        $memoryInfoFile = '/proc/meminfo';
        if (is_file($memoryInfoFile) && is_readable($memoryInfoFile)) {
            $memoryInfoFile = file($memoryInfoFile);
            $totalMemory = preg_grep("/^MemTotal:/", $memoryInfoFile);
            list($key, $size, $unit) = preg_split('/\s+/', $totalMemory[0], 3);
            $totalRam = number_format($size) . ' ' . $unit;
        }

        return $totalRam;
    }

    /**
     *  getCakePhpPlugins method
     *
     * @return array of CakePHP plugins
     */
    public function getCakePhpPlugins()
    {
        $plugins = Plugin::loaded();

        return $plugins;
    }

    /**
     * getCakePhpVersion method
     *
     * @return string CakePHP version
     */
    public function getCakePhpVersion()
    {
        $version = Configure::version();

        return $version;
    }

    /**
     *  getComposerPackages method
     *
     * @return array of installed composer packages
     */
    public function getComposerPackages()
    {
        //
        // Installed composer libraries (from composer.lock file)
        //
        $composerLock = ROOT . DS . 'composer.lock';
        $composer = null;
        if (is_readable($composerLock)) {
            $composer = json_decode(file_get_contents($composerLock), true);
        }
        $packages = !empty($composer['packages']) ? $composer['packages'] : [];

        return $packages;
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
        $matchCounts = [];
        foreach ($packages as $package) {
            // Concatenate all fields that we'll be matching against
            $matchString = $package['name'];
            if (!empty($package['description'])) {
                $matchString .= $package['description'];
            }
            foreach ($matchWords as $word) {
                if (empty($matchCounts[$word])) {
                    $matchCounts[$word] = 0;
                }
                if (preg_match('/' . $word . '/', $matchString)) {
                    $matchCounts[$word]++;
                }
            }
        }

        return $matchCounts;
    }

    /**
     *  getProjectLogo method
     *
     * @param string $logoSize of logo - mini or large
     * @return string base64 encoded project logo
     */
    public function getProjectLogo($logoSize = '')
    {
        $logoSize = in_array($logoSize, $this->logoSizes) ? $logoSize : $this->defaultLogoSize;
        $logo = Configure::read('Theme.logo.' . $logoSize);

        return $logo;
    }

    /**
     * getCopyright method
     *
     * @return string copyright
     */
    public function getCopyright()
    {
        $copyright = 'Copyright &copy; ' . date('Y') . ' ' . $this->getProjectName() . '. All rights reserved.';

        return $copyright;
    }
}
