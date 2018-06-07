<?php
namespace App\Model\Table;

use Rozdol\Number\Test;

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
     * op2numbers method
     *
     * @return \Cake\Http\Response
     */
    public function op2numbers($num1, $num2, $op='add')
    {
        $response = new Response();
        switch ($op) {
            case 'add':
                $response = $response->withStringBody(json_encode(['answer' => Test::sum2num($num1, $num2)]));
                break;
            
            default:
                throw new Exception("Unknown OP", 1);
                
                break;
        }
        

        return $response;
    }
}
