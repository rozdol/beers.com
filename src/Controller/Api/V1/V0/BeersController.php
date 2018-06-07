<?php
namespace App\Controller\Api\V1\V0;

use Cake\ORM\TableRegistry;

class BeersController extends AppController
{

    /**
     *
     * @return int
     */
    public function getDate()
    {
        //dd($this->request->query);
        $beers = TableRegistry::get('Beers');

        $params=$this->request->query;
        return $beers->op2numbers($params['a'], $params['b']);
    }
}
