<?php
namespace App\SystemInfo;

use InvalidArgumentException;

/**
 * Php class
 *
 * This is a helper class that assists with
 * fetching a variety of PHP information
 * from the system.
 */
class Php
{
    /**
     * Get current version of PHP or provided extension
     *
     * @param string $extension Optional extension, like 'curl'
     * @return string
     */
    public static function getVersion($extension = null)
    {
        return $extension ? phpversion($extension) : phpversion();
    }

    /**
     * Get current SAPI
     *
     * @return string
     */
    public static function getSapi()
    {
        return PHP_SAPI;
    }

    /**
     * Get a list of loaded PHP extensions
     *
     * @return array
     */
    public static function getLoadedExtensions()
    {
        $result = [];

        $extensions = get_loaded_extensions();
        asort($extensions);

        foreach ($extensions as $extension) {
            $result[$extension] = static::getVersion($extension);
        }

        return $result;
    }

    /**
     * Get current user
     *
     * This method returns the user which runs
     * the current PHP process.
     *
     * @return string
     */
    public static function getUser()
    {
        return get_current_user();
    }

    /**
     * Get path to PHP executable
     *
     * This method returns the path to the PHP
     * executable used to run the current script.
     * This can be PHP FPM, CLI, or a variety of
     * other options.
     *
     * @return string
     */
    public static function getBinary()
    {
        return PHP_BINARY;
    }

    /**
     * Get path to php.ini
     *
     * This method returns the full path to the
     * php.ini file which was used for the
     * current process.
     *
     * @return string
     */
    public static function getIniPath()
    {
        return php_ini_loaded_file();
    }

    /**
     * Get configuration value
     *
     * This method returns the value of the
     * given configuration key from the
     * php.ini.
     *
     * @param sting $configKey Configuration key to get the value for
     * @return mixed
     */
    public static function getIniValue($configKey)
    {
        return ini_get((string)$configKey);
    }

    /**
     * Convert value to bytes
     *
     * Convert sizes from PHP settings like post_max_size
     * for example 8M to integer number of bytes.
     *
     * If number is integer return as is.
     *
     * NOTE: This is a modified copy from qobo/cakephp-utils/config/bootstrap.php
     *
     * @throws \InvalidArgumentException when cannot convert
     * @param string|int $value Value to convert
     * @return int
     */
    public static function valueToBytes($value)
    {
        if (is_int($value)) {
            return $value;
        }

        $result = (string)$value;
        if (preg_match('/(\d+)K/i', $result, $matches)) {
            $result = $matches[1] * 1024;
        } elseif (preg_match('/(\d+)M/i', $result, $matches)) {
            $result = $matches[1] * 1024 * 1024;
        } elseif (preg_match('/(\d+)G/i', $result, $matches)) {
            $result = $matches[1] * 1024 * 1024 * 1024;
        } else {
            throw new InvalidArgumentException("Failed to find K, M, or G in a non-integer value [$result]");
        }

        return (int)$result;
    }

    /**
     * Get configuration setting for memory_limit
     *
     * @return int Memory limit in bytes
     */
    public static function getMemoryLimit()
    {
        $result = static::getIniValue('memory_limit');
        $result = static::valueToBytes($result);

        return $result;
    }

    /**
     * Get configuration setting for max_execution_time
     *
     * @return numeric Maximum execution time in seconds
     */
    public static function getMaxExecutionTime()
    {
        return static::getIniValue('max_execution_time');
    }

    /**
     * Get configuration setting for upload_max_filesize
     *
     * @return int Maximum upload file size in bytes
     */
    public static function getUploadMaxFilesize()
    {
        $result = static::getIniValue('upload_max_filesize');
        $result = static::valueToBytes($result);

        return $result;
    }

    /**
     * Get configuration setting for post_max_size
     *
     * @return int Max post size in bytes
     */
    public static function getPostMaxSize()
    {
        $result = static::getIniValue('post_max_size');
        $result = static::valueToBytes($result);

        return $result;
    }
}
