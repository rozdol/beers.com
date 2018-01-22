<?php
namespace App\Shell;

use App\Feature\Factory as FeatureFactory;
use Cake\Console\Shell;
use Cake\I18n\Time;
use Cake\Log\Log;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use DateTime;

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

        $lock = $this->Lock->lock(__FILE__, __CLASS__);

        $jobs = $this->ScheduledJobs->getActiveJobs();

        if (empty($jobs)) {
            $lock->unlock();

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

            $state = $instance->run($entity->options);
            $this->ScheduledJobLogs->logJob($entity, $state, $now);
        }

        $lock->unlock();
        $this->out('Lock released. Exiting...');
    }
}
