<?php
namespace App\Feature;

use Cake\Utility\Hash;
use InvalidArgumentException;

class Config
{
    protected $type;

    protected $name;

    protected $active;

    /**
     * Constructor method.
     *
     * @param array $config Feature config
     */
    public function __construct(array $config)
    {
        $type = Hash::get($config, 'type');
        if (!is_string($type)) {
            throw new InvalidArgumentException('Feature type must be a string.');
        }

        $name = Hash::get($config, 'name');
        if (!is_string($name)) {
            throw new InvalidArgumentException('Feature name must be a string.');
        }

        $active = Hash::get($config, 'active');
        if (!is_bool($active)) {
            throw new InvalidArgumentException('Feature active status must be a boolean.');
        }

        $this->type = $type;
        $this->name = $name;
        $this->active = (bool)$active;
    }

    /**
     * Feature type getter.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Feature name getter.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Feature active status getter.
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->active;
    }
}
