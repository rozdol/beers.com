<?php
namespace App\Controller\Api\V1\V0;

use Cake\Utility\Inflector;

class SystemController extends AppController
{
    public function info()
    {
        $data = $this->request->getData();
        $content = Inflector::underscore($data['tab']);

        $this->set(compact('content'));
        $this->render('/System/json/info');
    }
}
