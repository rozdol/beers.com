<?php
namespace App\Shell;

use App\Feature\Factory as FeatureFactory;
use Cake\Console\Shell;
use Cake\I18n\Time;
use Cake\Log\Log;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Qobo\Utils\Utility\Lock\FileLock;
use \DateTime;
use \Exception;
use \RuntimeException;

/**
 * Cron shell command.
 */
class CronShell extends Shell
{

    public $tasks = ['Lock'];

    protected $featureName = 'ScheduledJobs';

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {
        $feature = FeatureFactory::get('Module' . DS . $this->featureName);

        if (!$feature->isActive()) {
            throw new NotFoundException();
        }

        $this->info('Running cron...');
        $this->ScheduledJobs = TableRegistry::get('ScheduledJobs');
        $this->ScheduledJobLogs = TableRegistry::get('ScheduledJobLogs');

        $jobs = $this->ScheduledJobs->getActiveJobs();

        if (empty($jobs)) {
            return $this->out('No jobs found. Exiting...');
        }

        $now = Time::now();

        foreach ($jobs as $entity) {
            $rrule = $this->ScheduledJobs->getRRule($entity);
            $shouldRun = $this->ScheduledJobs->timeToInvoke($now, $rrule);

            if (!$shouldRun) {
                continue;
            }

            $instance = $this->ScheduledJobs->getInstance($entity->job, 'Job');

            if (!$instance) {
                Log::warning("Failed to instantiate Job [{$entity->job}]");
                continue;
            }

            try {
                $lock = $this->lock(__FILE__, $entity->job);
                $state = $instance->run($entity->options);
                $this->ScheduledJobLogs->logJob($entity, $state, $now);
            } catch (Exception $e) {
                $this->info("Job [{$entity->job}] is already locked.");
                continue;
            }
            $lock->unlock();
        }

        $this->info('Exiting cron shell...');
    }

    /**
     * Generate lock file. Abort if lock file is already generated.
     *
     * @param string $file Path to the shell script which acquires lock
     * @param string $class Name of the shell class which acquires lock
     * @return \Qobo\Utils\Utility\FileLock
     */
    public function lock($file, $class)
    {
        $class = str_replace(':', '_', $class);
        $lockFile = $this->Lock->getLockFileName($file, $class);

        try {
            $lock = new FileLock($lockFile);
        } catch (Exception $e) {
            $this->abort($e->getMessage());
        }

        if (!$lock->lock()) {
            throw new RuntimeException('Job already in process');
        }

        return $lock;
    }
}
