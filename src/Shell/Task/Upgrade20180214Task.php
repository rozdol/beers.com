<?php

namespace App\Shell\Task;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
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
            $this->migrateReports($module);

            $lists = $this->getLists($path . DS . $module . DS . 'lists');
            if (empty($lists)) {
                $this->info(sprintf('migrateLists skipped, lists not found in %s', $module));

                continue;
            }

            foreach ($lists as $list) {
                $file = new File($list);
                $this->migrateLists($module, $file->name());
            }
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
     * @param string $module Module name
     * @return void
     */
    private function migrateReports($module)
    {
        $config = new ModuleConfig(ConfigType::REPORTS(), $module);

        try {
            $source = new File($config->find());
        } catch (InvalidArgumentException $e) {
            $this->info(sprintf('%s skipped, reports not found in %s', __FUNCTION__, $module));

            return;
        }

        $dest = new File(
            $source->info()['dirname'] . DS . $source->info()['filename'] . '.' . static::EXTENSION,
            true
        );

        if (! $dest->exists()) {
            $this->abort(sprintf('Failed to create destination file "%s"', $dest->path));
        }

        $data = $config->parse();

        if (! $dest->write($this->toJSON($data))) {
            $this->abort(sprintf('Failed to write data on "%s"', $dest->path));
        }

        if (! $source->delete()) {
            $this->abort(sprintf('Failed to delete source file "%s"', $source->path));
        }
    }

    /**
     * Main method responsible for migrating {lists}.csv files to {lists}.json.
     *
     * @param string $module Module name
     * @param string $name Target list name
     * @return void
     */
    private function migrateLists($module, $name)
    {
        $config = new ModuleConfig(ConfigType::LISTS(), $module, $name);

        try {
            $source = new File($config->find());
        } catch (InvalidArgumentException $e) {
            $this->info(sprintf('%s skipped, lists not found in %s', __FUNCTION__, $module));

            return;
        }

        $dest = new File(
            $source->info()['dirname'] . DS . $source->info()['filename'] . '.' . static::EXTENSION,
            true
        );

        if (! $dest->exists()) {
            $this->abort(sprintf('Failed to create destination file "%s"', $dest->path));
        }

        $data = $config->parse();

        if (! $dest->write($this->toJSON($data))) {
            $this->abort(sprintf('Failed to write data on "%s"', $dest->path));
        }

        if (file_exists($source->Folder->path . DS . $source->info()['filename'])) {
            $dir = new Folder($source->Folder->path . DS . $source->info()['filename']);
            if (! $dir->delete()) {
                $this->abort(sprintf('Failed to delete directory "%s"', $dir->path));
            }
        }

        if (! $source->delete()) {
            $this->abort(sprintf('Failed to delete source file "%s"', $source->path));
        }
    }

    /**
     * Retrieves CSV lists files from specified directory.
     *
     * @param string $path Target directory, for example: /var/www/html/my-project/config/Modules/Articles/lists/
     * @return array
     */
    private function getLists($path)
    {
        $dir = new Folder($path);

        return $dir->find('.*\.csv');
    }
}
