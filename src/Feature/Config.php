<?php
namespace App\Feature;

use Cake\Utility\Hash;
use InvalidArgumentException;

class Config
{
    /**
     * Feature name.
     *
     * @var string
     */
    protected $name;

    /**
     * Active flag.
     *
     * @var bool
     */
    protected $active = true;

    /**
     * Constructor method.
     *
     * @param array $config Feature config
     */
    public function __construct(array $config)
    {
        $required = [
            'name' => ['type' => 'string'],
            'active' => ['type' => 'boolean'],
            'auth' => ['type' => 'object', 'instanceof' => '\Cake\Controller\Component\AuthComponent'],
            'request' => ['type' => 'object', 'instanceof' => '\Cake\Http\ServerRequest']
        ];

        foreach ($required as $field => $options) {
            if (!array_key_exists($field, $config)) {
                throw new InvalidArgumentException('Missing required parameter [' . $field . '].');
            }

            $givenType = gettype($config[$field]);
            if ($givenType !== $options['type']) {
                throw new InvalidArgumentException(
                    'Parameter [' . $field . '] must be of type [' . $options['type'] . '], [' . $givenType . '] given.'
                );
            }

            if (!empty($options['instanceof']) && !$config[$field] instanceof $options['instanceof']) {
                throw new InvalidArgumentException(
                    'Parameter [' . $field . '] must be instance of [' . $options['instanceof'] . '].'
                );
            }
        }

        foreach ($config as $field => $value) {
            $this->{$field} = $value;
        }
    }

    public function get($field)
    {
        $result = null;
        if (property_exists($this, $field)) {
            $result = $this->{$field};
        }

        return $result;
    }
}
