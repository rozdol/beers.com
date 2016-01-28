<?php

namespace App\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Shell\PluginShell;

/**
 * Custom Plugin Shell class that adds extended
 * functionality to Cake's core Plugin Shell.
 */
class QoboPluginShell extends PluginShell
{
    /**
     * Tasks to load
     *
     * @var array
     */
    public $tasks = [
        'List',
        'Migrate'
    ];

    /**
     * Get the option parser for this shell.
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser
            ->description('Qobo Plugin Shell adds extended functionality to Cake\'s Plugin Shell.')
            ->addSubcommand('list', ['help' => 'List all plugins', 'parser' => $this->List->getOptionParser()])
            ->addSubcommand('migrate', ['help' => 'Migrate all plugins', 'parser' => $this->Migrate->getOptionParser()]);

        return $parser;
    }
}
