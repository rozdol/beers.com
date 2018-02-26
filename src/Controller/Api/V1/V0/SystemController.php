<?php
namespace App\Controller\Api\V1\V0;

use Cake\Core\Configure;

class SystemController extends AppController
{
    /**
     * Return dynamic info tabs
     *
     * @return null|string response of HTML content
     */
    public function info()
    {
        $tabs = Configure::read('SystemInfo.tabs');

        $data = $this->request->getData();
        $content = '';
        if (!empty($data['tab']) && in_array($data['tab'], array_keys($tabs))) {
            $content = $data['tab'];
        }

        $this->set(compact('content'));
        $this->render('/System/json/info');
    }
}
