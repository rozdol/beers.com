<?php

namespace App\Shell;

use Cake\Cache\Cache;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;

/**
 * Clear framerwork's cache and outputs message on success or failure.
 */
class ClearCacheShell extends Shell
{
    /**
     * Clear cache for view elements
     *
     * @return bool
     */
    public function views()
    {
        // false flag clears all keys.
        Cache::clear(false);
        $this->out('<success>Clear all cached view elements.</success>');

        return true;
    }

    /**
     * Clear cache for models
     *
     * @return bool
     */
    public function models()
    {
        $conMan = ConnectionManager::get('default');
        $conMan->cacheMetadata(true);
        $schema = $conMan->schemaCollection();
        $tables = $schema->listTables();
        if (!$tables) {
            $this->error('Error! Cannot find tables.');
        }
        foreach ($tables as $table) {
            $key = $schema->cacheKey($table);
            Cache::delete($key, $schema->cacheMetadata());
        }
        $this->out('<success>Clear all cached models.</success>');

        return true;
    }
    /**
     * Clear all caching file of the framework. This includes
     * l10n, i18n and file maps.
     *
     * @return bool
     */
    public function core()
    {
        $core = Cache::config('_cake_core_');
        if (!$core) {
            $this->error('Error! Cannot find core config.');
            return false;
        }
        $dir = $core['path'];
        if (!file_exists($dir)) {
            $this->error('Error! Cannot proceed without the cache\'s path');
            return false;
        }
        $it = new \FilesystemIterator($dir, \FilesystemIterator::SKIP_DOTS);
        foreach ($it as $file) {
            $filename = str_replace($core['prefix'], '', $file->getFilename());
            Cache::delete($filename, '_cake_core_');
        }
        $this->out('<success>Clear all core caching files.</success>');

        return true;
    }

    /**
     * Clear all cache.
     *
     * @return bool
     */
    public function all()
    {
        $m = $this->models();
        $v = $this->views();
        $c = $this->core();

        if (!$m || !$v || !$c) {
             $this->err('Something when wrong.');
             return false;
        }

        return true;
    }

    /**
     * Get the option parser for this shell.
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser
            ->addSubcommand('views', ['help' => 'Clear View elements\' cache.'])
            ->addSubcommand('models', ['help' => 'Clear Models\' cache.'])
            ->addSubcommand('core', ['help' => 'Clear l10n, i18n and file maps\' cache',])
            ->addSubcommand('all', ['help' => 'Clear all cache.']);

        return $parser;
    }
}
