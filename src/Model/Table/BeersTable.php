<?php
namespace App\Model\Table;

use App\Random\Test;

use Cake\Http\Response;

class BeersTable extends AppTable
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

        $this->table('beers');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * getDate method
     *
     * @return \Cake\Http\Response
     */
    public function getDate()
    {
        $response = new Response();
        $response = $response->withStringBody(json_encode(['Random' => Test::randomDate()]));

        return $response;
    }
}
