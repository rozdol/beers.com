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

    /**
     * CSV modules configurations path.
     *
     * @var string
     */
    private $path = '';

    /**
     * Configure option parser
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->description('Migration of INI/CSV configuration files to JSON');

        return $parser;
    }

    /**
     * Main method.
     *
     * @return void
     */
    public function main()
    {
        $this->path = Configure::readOrFail('CsvMigrations.modules.path');
        $this->validatePath();
        // remove trailing slash
        $this->path = rtrim($this->path, DS);

        // fetch modules
        $modules = Utility::findDirs($this->path);

        foreach ($modules as $module) {
            $this->migrateToJSON($module);
            $this->mergeWithFieldsJSON($module);
        }

        $this->success(sprintf('%s completed.', $this->getOptionParser()->getDescription()));
    }

    /**
     * Validates CSV modules path.
     *
     * @return void
     */
    private function validatePath()
    {
        if (! is_string($this->path)) {
            $this->abort('$path must be a string');
        }

        if ('' === trim($this->path)) {
            $this->abort('$path cannot be empty');
        }

        if (0 !== strpos($this->path, ROOT)) {
            $this->abort('$path does not reside in project directory');
        }
    }

    /**
     * Handles iteration of configuration list and initialization of migrations to JSON.
     *
     * @param string $module Module name
     * @return void
     */
    private function migrateToJSON($module)
    {
        // configuration list to iterate through and run the migrations from CSV/INI to JSON.
        $configList = [
            ['type' => ConfigType::REPORTS()], // reports.ini
            ['type' => ConfigType::FIELDS()], // fields.ini
            ['type' => ConfigType::MIGRATION()], // migration.csv
            ['type' => ConfigType::MODULE()], // config.ini
            ['type' => ConfigType::LISTS(), 'multi' => ['dir' => 'lists', 'ext' => 'csv']], // {lists}.csv
            ['type' => ConfigType::VIEW(), 'multi' => ['dir' => 'views', 'ext' => 'csv']] // {views}.csv
        ];

        // loops through configuration list and executes migration
        foreach ($configList as $config) {
            if (! isset($config['multi'])) {
                $this->singleFileMigration($config['type'], $module);
            }

            if (isset($config['multi'])) {
                $this->multiFileMigration($config['type'], $module, $config['multi']);
            }
        }
    }

    /**
     * Prepares single file for migration (used for reports.ini, migration.csv, fields.ini and config.ini).
     *
     * @param \Qobo\Utils\ModuleConfig\ConfigType $type ConfigType enum
     * @param string $module Module name
     * @param string $filename Optional filename
     * @return void
     */
    private function singleFileMigration(ConfigType $type, $module, $filename = '')
    {
        if ($this->migrate($type, $this->getConfig($type, $module, $filename))) {
            $this->success(sprintf('Migrated %s for %s module', $filename ? $filename : $type, $module));

            return;
        }

        $this->info(sprintf('Migrate %s skipped, no relevant files found in %s module', $type, $module));
    }

    /**
     * Prepares multiple files for migration (used for lists/ and views/ directory files).
     *
     * @param \Qobo\Utils\ModuleConfig\ConfigType $type ConfigType enum
     * @param string $module Module name
     * @param array $config Multi files configuration
     * @return void
     */
    private function multiFileMigration(ConfigType $type, $module, array $config)
    {
        $path = $this->path . DS . $module . DS . $config['dir'];

        $files = $this->getFilesByType($path, $config['ext']);
        if (empty($files)) {
            $this->info(sprintf('Migrate %s skipped, no relevant files found in %s module', $type, $module));

            return;
        }

        foreach ($files as $file) {
            $file = new File($file);
            $this->singleFileMigration($type, $module, $file->name());
        }
    }

    /**
     * Executes migration logic.
     *
     * @param \Qobo\Utils\ModuleConfig\ConfigType $type ConfigType enum
     * @param \Qobo\Utils\ModuleConfig\ModuleConfig $config Module config instance
     * @return bool
     */
    private function migrate(ConfigType $type, ModuleConfig $config)
    {
        $source = $this->getFileByConfig($config);

        if (is_null($source)) {
            return false;
        }

        $dest = new File($source->info()['dirname'] . DS . $source->info()['filename'] . '.' . static::EXTENSION, true);
        if (! $dest->exists()) {
            $this->abort(sprintf('Failed to create destination file "%s"', $dest->path));
        }

        if (! $dest->write($this->toJSON($config->parse()))) {
            $this->abort(sprintf('Failed to write on destination file "%s"', $dest->path));
        }

        // special case for handling deletions of a list's related sub-list(s)
        if (ConfigType::LISTS() === $type) {
            $this->deleteNestedLists($source);
        }

        if (! $source->delete()) {
            $this->abort(sprintf('Failed to delete source file "%s"', $source->path));
        }

        return true;
    }

    /**
     * Method responsible for merging 'migration.json' data into 'fields.json'.
     * If merge is successful, then it proceeds with the deletion of 'migration.json'.
     *
     * @param string $module Module name
     * @return void
     */
    private function mergeWithFieldsJSON($module)
    {
        $source = $this->getFileByConfig($this->getConfig(ConfigType::MIGRATION(), $module, 'migration.json'));
        if (is_null($source)) {
            $this->info(sprintf('Merge skipped, no "migration.json" file found in %s module', $module));

            return;
        }

        $dest = $this->getFileByConfig($this->getConfig(ConfigType::FIELDS(), $module, 'fields.json'));
        // if 'fields.json' does not exist, which it might be the case in some projects, create it
        if (is_null($dest)) {
            $dest = new File($this->path . DS . $module . DS . 'config' . DS . 'fields.' . static::EXTENSION, true);
        }

        if (! $dest->exists()) {
            $this->abort(sprintf('Failed to create destination file "%s"', $dest->path));
        }

        $data = array_merge_recursive(
            (array)json_decode($source->read(), true),
            (array)json_decode($dest->read(), true)
        );

        if (! $dest->write($this->toJSON($data))) {
            $this->abort(sprintf('Failed to write on destination file "%s"', $dest->path));
        }

        if (! $source->delete()) {
            $this->abort(sprintf('Failed to delete source file "%s"', $source->path));
        }

        $this->success(sprintf('Merged migration.json with fields.json for %s module', $module));
    }

    /**
     *  Retrieves module configuration by specified type.
     *
     * @param \Qobo\Utils\ModuleConfig\ConfigType $type ConfigType enum
     * @param string $module Module name
     * @param string $configFile Optional config file name
     * @return \Qobo\Utils\ModuleConfig\ModuleConfig
     */
    private function getConfig(ConfigType $type, $module, $configFile = '')
    {
        return new ModuleConfig($type, $module, $configFile);
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
     * Returns File instance of module configuration file.
     *
     * @param \Qobo\Utils\ModuleConfig\ModuleConfig $config Module config instance
     * @return \Cake\Filesystem\File|null
     */
    private function getFileByConfig(ModuleConfig $config)
    {
        try {
            return new File($config->find());
        } catch (InvalidArgumentException $e) {
            //
        }

        return null;
    }

    /**
     * Retrieves files from specified directory, by type.
     *
     * @param string $path Target directory, for example: /var/www/html/my-project/config/Modules/Articles/lists/
     * @param string $type Target file type, for example: csv, ini, json
     * @return array
     */
    private function getFilesByType($path, $type = 'csv')
    {
        $dir = new Folder($path);

        return $dir->find(sprintf('.*\.%s', $type));
    }

    /**
     * Handles deletion of a list's nested lists.
     *
     * @param \Cake\Filesystem\File $file File instance
     * @return void
     */
    private function deleteNestedLists(File $file)
    {
        $path = $file->Folder->path . DS . $file->info()['filename'];

        if (! file_exists($path)) {
            return;
        }

        $dir = new Folder($path);
        if ($dir->delete()) {
            return;
        }

        $this->abort(sprintf('Failed to delete nested lists in %s', $path));
    }
}
