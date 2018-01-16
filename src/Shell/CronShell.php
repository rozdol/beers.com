<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use RRule\RfcParser;
use RRule\RRule;

/**
 * Cron shell command.
 */
class CronShell extends Shell
{

    public $tasks = ['Lock'];

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
        $this->info('Running cron...');
        $this->ScheduledJobs = TableRegistry::get('ScheduledJobs');

        $lock = $this->Lock->lock(__FILE__, __CLASS__);

        $jobs = $this->ScheduledJobs->getActiveJobs();

        if (empty($jobs)) {
            $lock->unlock();
            return $this->out('No jobs found. Exiting...');
        }

        foreach ($jobs as $entity) {
            $instance = $this->ScheduledJobs->getInstance($entity->job, 'Job');

            if (!$instance) {
                continue;
            }

            $rrule = $this->getRRule($entity);

            if ($this->ScheduledJobs->isTimeToRun($entity, $rrule)) {
                $state = $instance->run($entity->options);
                pr($state);
                // @TODO: saving state response of shell execution.
            }
        }

        $lock->unlock();
        $this->out('Lock released. Exiting...');
    }

    /**
     * Get RRule object based on entity
     *
     * @param \Cake\Datasource\EntityInterface $entity of the job
     *
     * @return \RRule\RRule $rrule to be used
     */
    protected function getRRule(EntityInterface $entity)
    {
        $rrule = null;

        if (!empty($entity->recurrence)) {
            if (!empty($entity->start_date)) {
                $config = RfcParser::parseRRule($entity->recurrence, $entity->start_date);
            } else {
                $config = RfcParser::parseRRule($entity->recurrence);
            }

            $rrule = new RRule($config);
        }

        return $rrule;
    }
}
