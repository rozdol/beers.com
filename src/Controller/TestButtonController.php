<?php
namespace App\Controller;

use App\Controller\AppController;

use Rozdol\Number\Test;

/**
 * TestButton Controller
 *
 *
 * @method \App\Model\Entity\TestButton[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TestButtonController extends AppController
{

    /**
     *
     * @return responce
     */
    public function display()
    {
        $params=$this->request->query;
        $response = $this->response
        ->withDisabledCache()
        ->withType('application/json');
        // mutable inner Body
        $bytes = $response->getBody()->write(json_encode(['TEST'=>Test::sum2num($params['a'], $params['b'])]));
        // immutable Response
        return $response;
    }
}
