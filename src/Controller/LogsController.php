<?php
namespace App\Controller;

use DatabaseLog\Controller\Admin\LogsController as BaseController;

class LogsController extends BaseController
{
    /**
     * Initialization hook method.
     *
     * Implement this method to avoid having to overwrite
     * the constructor and call parent.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->paginate['limit'] = 10;
        $this->paginate['fields'] = null;
    }
}
