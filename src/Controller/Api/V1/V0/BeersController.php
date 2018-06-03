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
        $beers = TableRegistry::get('Beers');

        return $beers->getDate();
    }
}
