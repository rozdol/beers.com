<?php
namespace App\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;
use RRule\RRule;
use RuntimeException;

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

    /**
     * Get Activated Job records
     *
     * @return array $result containing record entities.
     */
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
     * @return bool $state whether to run it or not.
     */
    public function isTimeToRun(EntityInterface $entity, RRule $rrule)
    {
        $state = true;

        return $state;
    }

    /**
     * Get List of Existing Jobs
     *
     * Iterate through all Handlers and ask for jobs list
     *
     * @param array $options if any needed
     *
     * @return array $result of scripts for UI.
     */
    public function getList(array $options = [])
    {
        $result = $handlers = [];

        $namespace = 'App\\ScheduledJobs\\Handlers\\';
        $path = dirname(dirname(dirname(__FILE__))) . DS . 'ScheduledJobs' . DS . 'Handlers';

        $handlers = $this->scanDir($path);

        foreach ($handlers as $handlerName) {
            $class = $namespace . $handlerName;

            try {
                $object = new $class();

                $result = array_merge($result, $object->getList());
            } catch (RuntimeException $e) {
                pr($e->getMessage());
            }
        }

        return $result;
    }

    /**
     * List Handlers in the directory
     *
     * @param string $path of the directory
     */
    protected function scanDir($path)
    {
        $result = [];
        $dir = new Folder($path);
        $contents = $dir->read(true, true);

        if (empty($contents[1])) {
            return $result;
        }

        foreach ($contents[1] as $file) {
            if (substr($file, -4) !== '.php') {
                continue;
            }

            if (preg_match('/^Abstract/', $file)) {
                continue;
            }

            $result[] = substr($file, 0, -4);
        }

        return $result;
    }
}
