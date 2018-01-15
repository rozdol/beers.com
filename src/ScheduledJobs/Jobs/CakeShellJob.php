<?php
namespace App\ScheduledJobs\Jobs;

use App\ScheduledJobs\Jobs\JobInterface;

class CakeShellJob implements JobInterface
{
    protected $operator = './bin/cake';

    protected $command = '';

    protected $arguments = '';

    public function __construct($command = '')
    {
        $this->command = $command;
    }

    public function run($arguments = null)
    {
        $state = [];

        return "./bin/cake " . $this->command . " " . $this->arguments;
    }
}
