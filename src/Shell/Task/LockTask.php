<?php
namespace App\Shell\Task;

use Cake\Console\Shell;
use Cake\Utility\Inflector;
use Exception;
use Qobo\Utils\Utility\FileLock;

class LockTask extends Shell
{
    /**
     * Generate lock file. Abort if lock file is already generated.
     *
     * @param string $file optional lockfile name
     * @return \Qobo\Utils\Utility\FileLock
     */
    public function lock($file, $class)
    {
        $class = Inflector::underscore(preg_replace('/\\\/', '', $class));
        $lockFile = $class . '_' . md5($file) . '.lock';

        try {
            $lock = new FileLock($lockFile);
        } catch (Exception $e) {
            $this->abort($e->getMessage());
        }

        if (!$lock->lock()) {
            $this->abort('Task is already in progress');
        }

        return $lock;
    }
}
