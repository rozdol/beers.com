<?php
namespace App\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Utility\Inflector;
use RRule\RRule;

class ScheduledJobsTable extends AppTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('scheduled_jobs');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
    }

    public function getActiveJobs()
    {
        $result = [];

        $query = $this->find()
            ->where(['active' => 1])
            ->order(['priority' => 'ASC']);

        $entities = $query->all();

        if (empty($entities)) {
            return $result;
        }

        $result = $entities;

        return $result;
    }

    /**
     * Get Job Instance
     *
     * Retrieve job object that can be run
     *
     * @param string $command from DB entity
     *
     * @return \App\ScheduledJobs\JobInterface $instance of the job.
     */
    public function getInstance($command = null, $type = null)
    {
        $instance = null;

        if (empty($command) || empty($type)) {
            return $instance;
        }

        list($handlerName, $shellCommand) = explode('::', $command, 2);

        $dir = Inflector::camelize(Inflector::pluralize($type));
        $suffix = Inflector::camelize(Inflector::singularize($type));

        $path = dirname(dirname(dirname(__FILE__))) . DS . 'ScheduledJobs' . DS . $dir . DS;
        $path = $path . $handlerName . $suffix . '.php';

        if (file_exists($path)) {
            $class = 'App\\ScheduledJobs\\' . $dir . '\\' . $handlerName . $suffix;
            $instance = new $class($command);
        }

        return $instance;
    }

    /**
     * Is Time To Run the command
     *
     * @param \Cake\Datasource\EntityInterface $entity of the job
     * @param \RRule\RRule $rrule of the recurrence if any
     *
     * @return boolean $state whether to run it or not.
     */
    public function isTimeToRun(EntityInterface $entity, RRule $rrule)
    {
        $state = true;


        return $state;
    }
}
