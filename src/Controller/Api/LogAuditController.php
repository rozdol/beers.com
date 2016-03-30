<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;

class LogAuditController extends AppController
{
    /**
     * Return log audit results for specific record.
     *
     * @return void
     */
    public function changelog()
    {
        $result = [];
        if (isset($this->request->query['query'])) {
            $id = $this->request->query['query'];
            $query = $this->LogAudit->findByPrimaryKey($id)->order(['LogAudit.timestamp' => 'DESC']);
            $result = $query->toArray();
        }

        $this->set('changelog', $result);
        $this->set('_serialize', ['changelog']);
    }
}
