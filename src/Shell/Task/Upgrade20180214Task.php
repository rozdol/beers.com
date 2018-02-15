<?php
namespace App\Shell\Task;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;
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
        $modules = Utility::findDirs($path);

        // migrate reports.ini
        foreach ($modules as $module) {
            $this->migrate('reports', $this->getReportsConfig($module), $module);
        }

        // migrate {lists}.csv
        foreach ($modules as $module) {
            $lists = $this->getLists($path . DS . $module . DS . 'lists');
            if (empty($lists)) {
                $this->info(sprintf('Migrate Lists skipped, relevant files not found in %s module', $module));

                continue;
            }

            foreach ($lists as $list) {
                $file = new File($list);
                $this->migrate('lists', $this->getListsConfig($module, $file->name()), $module);
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
     *
     * @param string $module Module name
     * @return \Qobo\Utils\ModuleConfig\ModuleConfig
     */
    private function getReportsConfig($module)
    {
        return new ModuleConfig(ConfigType::REPORTS(), $module);
    }

    /**
     *
     * @param string $module Module name
     * @param string $list List name
     * @return \Qobo\Utils\ModuleConfig\ModuleConfig
     */
    private function getListsConfig($module, $list)
    {
        return new ModuleConfig(ConfigType::LISTS(), $module, $list);
    }

    /**
     * Main method responsible for migrating {lists}.csv files to {lists}.json.
     *
     * @param string $type File category type [lists, reports, migrations, fields]
     * @param \Qobo\Utils\ModuleConfig\ModuleConfig $config Module config instance
     * @param string $module Module name
     * @return void
     */
    private function migrate($type, $config, $module)
    {
        $source = $this->getSourceFile($config);

        if (is_null($source)) {
            $this->info(sprintf(
                '%s skipped, relevant files not found in %s module',
                Inflector::humanize(Inflector::delimit(__FUNCTION__)),
                $module
            ));

            return;
        }

        $dest = $this->getDestFile($source);
        if (! $dest->exists()) {
            $this->abort(sprintf('Failed to create destination file "%s"', $dest->path));
        }

        if (! $this->writeToDestFile($dest, $config)) {
            $this->abort(sprintf('Failed to write data on destination file "%s"', $dest->path));
        }

        if (! $this->deleteSourceFile($source, $type)) {
            $this->abort(sprintf('Failed to delete source file "%s"', $source->path));
        }
    }

    /**
     *
     * @param \Qobo\Utils\ModuleConfig\ModuleConfig $config Module config instance
     * @return \Cake\Filesystem\File|null
     */
    private function getSourceFile(ModuleConfig $config)
    {
        try {
            return new File($config->find());
        } catch (InvalidArgumentException $e) {
            //
        }

        return null;
    }

    /**
     *
     * @param \Cake\Filesystem\File $source Source instance
     * @return \Cake\Filesystem\File
     */
    private function getDestFile(File $source)
    {
        return new File($source->info()['dirname'] . DS . $source->info()['filename'] . '.' . static::EXTENSION, true);
    }

    /**
     *
     * @param \Cake\Filesystem\File $dest Destination instance
     * @param \Qobo\Utils\ModuleConfig\ModuleConfig $config Module config instance
     * @return bool
     */
    private function writeToDestFile(File $dest, ModuleConfig $config)
    {
        $data = $this->toJSON($config->parse());

        return $dest->write($data);
    }

    /**
     *
     * @param \Cake\Filesystem\File $source Source instance
     * @param string $type File category type [lists, reports, migrations, fields]
     * @return bool
     */
    private function deleteSourceFile(File $source, $type)
    {
        switch ($type) {
            case 'lists':
                $path = $source->Folder->path . DS . $source->info()['filename'];
                if (file_exists($path)) {
                    $dir = new Folder($path);
                    if (! $dir->delete()) {
                        return false;
                    }
                }
                break;
        }

        return $source->delete();
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
