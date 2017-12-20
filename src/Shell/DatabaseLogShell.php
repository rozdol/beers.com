<?php
namespace App\Shell;

use Cake\Core\Configure;
use Cake\I18n\Time;
use DatabaseLog\Shell\DatabaseLogShell as BaseShell;

class DatabaseLogShell extends BaseShell
{
    /**
     * Deletes log records older than specified time (maxLength).
     *
     * @return void
     */
    public function gc()
    {
        $age = Configure::read('DatabaseLog.maxLength');
        if (!$age) {
            $this->info('Required parameter "maxLength" is not defined (garbage collector)');

            return;
        }

        $date = new Time($age);

        $count = $this->DatabaseLogs->deleteAll(['created <' => $date]);

        $this->info($count . ' outdated logs removed (garbage collector)');
    }

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addSubcommand('gc', [
            'help' => 'Garbage collector.',
        ]);

        return $parser;
    }
}
