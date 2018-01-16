<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;
use RRule\RRule;
use RRule\RfcParser;

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
            $this->info('No jobs found.Exiting...');
            exit;
        }

        foreach ($jobs as $job) {
            $rrule = null;

            if (!empty($job['recurrence'])) {
                if (!empty($job['start_date'])) {
                    $config = RfcParser::parseRRule($job['recurrence'], $job['start_date']);
                } else {
                    $config = RfcParser::parseRRule($job['recurrence']);
                }

                $rrule = new RRule($config);
            }

            $instance = $this->ScheduledJobs->getInstance($job->command, 'Job');

            $handler = $this->ScheduledJobs->getInstance($job->command, 'Handler');
            dd([$instance, $handler]);
            if ($this->ScheduledJobs->isTimeToRun($job, $rrule)) {
                $state = $instance->run($job['arguments']);

                //$this->ScheduledJobs->log($state);
            }
        }

        $lock->unlock();
        $this->out('Lock released. Exiting...');
        /*
            1. [x]Locker::on()
            2. [x]$table->getActiveJobs();
            3. From entity: RuleObject $rrule, ArgumentInterface $argsObject, CommandInterface $command.
            4. CronShell.php checks RuleOBject (run or not).
            if ($rrule->isOk()) {
                5. JobRunner->run($command, $Arguments);
            }
        */
    }
}
