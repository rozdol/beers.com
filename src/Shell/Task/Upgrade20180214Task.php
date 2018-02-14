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
 *  This class is responsible for handling migration of INI configurations to JSON.
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
        $parser->description('Migrates INI configuration files to JSON');

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

    private function getModulePath($path, $module)
    {
        return $path . DS . $module;
    }

    private function getSourcePath($config, $path, $module)
    {
        return $this->getModulePath($path, $module) . DS . $config['dir'] . DS . $config['name'] . '.' . $config['ext'];
    }

    private function getDestPath($config, $path, $module)
    {
        return $this->getModulePath($path, $module) . DS . $config['dir'] . DS . $config['name'] . '.' . static::EXTENSION;
    }

    private function toJSON($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

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

        if (! $dest->write($this->toJSON($this->parseReports($module)))) {
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

    private function parseReports($module)
    {
        $config = new ModuleConfig(ConfigType::REPORTS(), $module);

        return $config->parse();
    }
}
