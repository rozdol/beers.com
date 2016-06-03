<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class LogAuditController extends AppController
{
    /**
     * Return log audit results for specific record.
     *
     * @return void
     */
    public function changelog($modelName, $id)
    {
        /*
        ideally we want to group by user and timestamp, but having user in the meta information makes this non-trivial
        for now we are using just the timestamp assuming that different will edit the same record at the same time is
        very unlikely.
         */
        $query = $this->LogAudit->findByPrimaryKey($id)
            ->order(['LogAudit.timestamp' => 'DESC'])
            ->group('LogAudit.timestamp');

        $entity = TableRegistry::get($modelName)->findById($id)->first();

        $this->set('changelog', $this->paginate($query));
        $this->set('modelName', $modelName);
        $this->set('entity', $entity);
        $this->set('_serialize', ['changelog']);
    }
}
