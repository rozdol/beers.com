<?php

namespace App\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;

/**
 *
 */
class SystemInfoHelper extends Helper
{
    /**
     * @var $projectName
     */
    protected $projectName = null;

    /**
     * @var $projectUrl
     */
    protected $projectUrl = null;

    /**
     * @var $projectVersion
     */
    protected $projectVersion = null;

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
}
