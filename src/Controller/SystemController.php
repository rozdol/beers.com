<?php
namespace App\Controller;

/**
 * System Controller
 */
class SystemController extends AppController
{
    /**
     * Display system information
     *
     * This action displays a variety of useful system
     * information, like project name, URL, version,
     * installed plugins, composer libraries, PHP version,
     * PHP configurations, server environment, etc.
     *
     * @todo Project name and URL should be refactored into application-wide settings
     */
    public function info()
    {
        //
        // Project information
        //

        // Use PROJECT_NAME environment variable or project folder name
        $projectName = getenv('PROJECT_NAME') ?: basename(ROOT);
        $this->set('projectName', $projectName);

        // Use PROJECT_URL environment variable or fallback URL
        $projectUrl = getenv('PROJECT_URL') ?: 'https://github.com/QoboLtd/project-template-cakephp';
        $this->set('projectUrl', $projectUrl);

        //
        // Versions
        //

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
        $this->set('versions', $versions);
    }
}
