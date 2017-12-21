<?php
namespace App\Log\Engine;

use Cake\Database\Log\LoggedQuery;
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
        // avoid logging database queries, which results in infinite recursion
        if ($message instanceof LoggedQuery) {
            return false;
        }

        if (!in_array($level, $this->levels())) {
            return false;
        }

        return parent::log($level, $message, $context);
    }
}
