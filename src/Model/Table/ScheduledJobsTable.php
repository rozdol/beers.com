<?php
namespace App\Model\Table;

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

}
