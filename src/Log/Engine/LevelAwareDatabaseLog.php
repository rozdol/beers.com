<?php
namespace App\Log\Engine;

use DatabaseLog\Log\Engine\DatabaseLog;

class LevelAwareDatabaseLog extends DatabaseLog
{
    /**
     * Skip writing logs if log level is not supported.
     *
     * {@inheritDoc}
     */
    public function log($level, $message, array $context = [])
    {
        if (!in_array($level, $this->levels())) {
            return false;
        }

        return parent::log($level, $message, $context);
    }
}
