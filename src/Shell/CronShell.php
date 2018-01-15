<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

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
            $rrule = new RRule($job['recurrence']);
            dd($rrule);
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
