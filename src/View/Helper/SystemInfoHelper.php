<?php
namespace App\View\Helper;

use App\SystemInfo\Project;

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
     *  getProjectLogo method
     *
     * @param string $logoSize of logo - mini or large
     * @return string HTML img tag with project logo
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
