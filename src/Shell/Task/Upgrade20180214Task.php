<?php

namespace App\Shell\Task;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use InvalidArgumentException;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;
use Qobo\Utils\Utility;
use RuntimeException;

/**
 *  This class is responsible for handling migration of INI/CSV configurations to JSON.
 */
class Upgrade20180214Task extends Shell
{
    const EXTENSION = 'json';

    private $basePath = '';
    private $config = [
        'fields' => [
            'dir' => 'config',
            'name' => 'fields',
            'ext' => 'ini'
        ],
        'lists' => [
            'dir' => 'lists',
            'name' => '%s',
            'ext' => 'csv'
        ],
        'migrations' => [
            'dir' => 'db',
            'name' => 'migration',
            'ext' => 'csv'
        ]
    ];

    /**
     * Configure option parser
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->description('Migrates INI/CSV configuration files to JSON');

        return $parser;
    }

    /**
     *
     *
     * @return void
     */
    public function main()
    {
        $path = Configure::read('CsvMigrations.modules.path');
        if (! $this->isValidPath($path)) {
            $this->err(
                sprintf('Invalid modules path, skipping task that "%s"', $this->getOptionParser()->getDescription())
            );

            return;
        }

        $path = rtrim($path, DS);

        foreach (Utility::findDirs($path) as $module) {
            $this->migrateReports($path, $module);
        }
    }

    /**
     * Validates CSV modules path.
     *
     * @param string $path CSV modules path
     * @return bool
     */
    private function isValidPath($path)
    {
        if (! is_string($path)) {
            return false;
        }

        if ('' === trim($path)) {
            return false;
        }

        if (0 !== strpos($path, ROOT)) {
            return false;
        }

        return true;
    }

    /**
     * CSV Module path getter, example: /var/www/html/my-project/config/Modules/Articles
     *
     * @param string $path CSV modules base path, example: /var/www/html/my-project/config/Modules
     * @param string $module Module name
     * @return string
     */
    private function getModulePath($path, $module)
    {
        return $path . DS . $module;
    }

    /**
     * Source file path getter, example: /var/www/html/my-project/config/Modules/Articles/config/reports.ini
     *
     * @param array $config File config based on type, for example: ['dir' => 'config', 'name' => 'reports', 'ext' => 'ini']
     * @param string $path CSV modules base path, example: /var/www/html/my-project/config/Modules
     * @param string $module Module name
     * @return string
     */
    private function getSourcePath($config, $path, $module)
    {
        return $this->getModulePath($path, $module) . DS . $config['dir'] . DS . $config['name'] . '.' . $config['ext'];
    }

    /**
     * Destination file path getter, example: /var/www/html/my-project/config/Modules/Articles/config/reports.json
     *
     * @param array $config File config based on type, for example: ['dir' => 'config', 'name' => 'reports', 'ext' => 'ini']
     * @param string $path CSV modules base path, example: /var/www/html/my-project/config/Modules
     * @param string $module Module name
     * @return string
     */
    private function getDestPath($config, $path, $module)
    {
        return $this->getModulePath($path, $module) . DS . $config['dir'] . DS . $config['name'] . '.' . static::EXTENSION;
    }

    /**
     * Converts data into JSON.
     *
     * @param mixed $data Source file data
     * @return string
     */
    private function toJSON($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * Main method responsible for migrating reports.ini files to reports.json.
     *
     * @param string $path CSV modules base path, example: /var/www/html/my-project/config/Modules
     * @param string $module Module name
     * @return void
     */
    private function migrateReports($path, $module)
    {
        $config = ['dir' => 'config', 'name' => 'reports', 'ext' => 'ini'];

        $source = new File($this->getSourcePath($config, $path, $module));
        if (! $source->exists()) {
            $this->info(
                sprintf('Skipping "%s": source file "%s" does not exist', __FUNCTION__, $source->path)
            );

            return;
        }

        $dest = new File($this->getDestPath($config, $path, $module), true);
        if (! $dest->exists()) {
            $this->err(
                sprintf('Skipping "%s": failed to create destination file "%s"', __FUNCTION__, $dest->path)
            );

            return;
        }

        if (! $dest->write($this->toJSON($this->parse(ConfigType::REPORTS(), $module)))) {
            $this->err(
                sprintf('Skipping "%s": failed to write data on "%s"', __FUNCTION__, $dest->path)
            );

            $dest->delete();

            return;
        }

        if ($source->delete()) {
            $this->warn(sprintf('Failed to delete source file "%s"', $source->path));
        }
    }

    /**
     * Parses and returns module configuration by type.
     *
     * @param \Qobo\Utils\ModuleConfig\ConfigType $type Configuration type
     * @param string $module Module name
     * @return \stdClass
     */
    private function parse(ConfigType $type, $module)
    {
        $config = new ModuleConfig($type, $module);

        return $config->parse();
    }
}
