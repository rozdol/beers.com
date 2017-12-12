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
        $lockFile = $this->getLockFileName($file, $class);

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

    /**
     * getLockFileName method
     *
     * @param string $fileName - path to the shell script which acquires lock
     * @param string $className - name of the shell class which acquires lock
     * @return string - unique lock file name
     */
    private function getLockFileName($fileName, $className)
    {
        $className = Inflector::underscore(preg_replace('/\\\/', '', $className));
        $lockFile = $className . '_' . md5($fileName) . '.lock';

        return $lockFile;
    }
}
