<?php

namespace App\ScheduledJobs\Jobs;

interface JobInterface
{
    public function run($arguments = null);
}
