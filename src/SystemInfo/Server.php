<?php
namespace App\SystemInfo;

/**
 * Server class
 *
 * This is a helper class that assists with
 * fetching a variety of server information
 * from the system.
 */
class Server
{
    /**
     * Get server information
     *
     * This method returns a number of
     * human-friendly metrics and facts about
     * the server.
     *
     * @return array
     */
    public static function getInfo()
    {
        $result = [
            'Hostname' => gethostname(),
            'Operating System' => static::getOperatingSystem(),
            'Machine Type' => static::getMachineType(),
            'Number of CPUs' => static::getNumberOfCpus(),
            'Total RAM' => static::getTotalRam(),
        ];

        return $result;
    }

    /**
     * Get server operating system name and release
     *
     * @return string
     */
    public static function getOperatingSystem()
    {
        return php_uname('s') . ' ' . php_uname('r');
    }

    /**
     * Get server machine type / hardware architecture
     *
     * @return string
     */
    public static function getMachineType()
    {
        return php_uname('m');
    }

    /**
     * Get server CPU count
     *
     * @return int
     */
    public static function getNumberOfCpus()
    {
        $result = 0;
        $cpuInfoFile = '/proc/cpuinfo';
        if (is_file($cpuInfoFile) && is_readable($cpuInfoFile)) {
            $cpuInfoFile = file($cpuInfoFile);
            $cpus = preg_grep("/^processor/", $cpuInfoFile);
            $result = count($cpus);
        }

        return $result;
    }

    /**
     * Get server total RAM size
     *
     * @return string
     */
    public static function getTotalRam()
    {
        $result = 'N/A';
        $memoryInfoFile = '/proc/meminfo';
        if (is_file($memoryInfoFile) && is_readable($memoryInfoFile)) {
            $memoryInfoFile = file($memoryInfoFile);
            $totalMemory = preg_grep("/^MemTotal:/", $memoryInfoFile);
            list($key, $size, $unit) = preg_split('/\s+/', $totalMemory[0], 3);
            $result = number_format($size) . ' ' . $unit;
        }

        return $result;
    }
}
