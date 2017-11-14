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
        $name = Hash::get($config, 'name');
        if (!is_string($name)) {
            throw new InvalidArgumentException('Feature name must be a string.');
        }

        $active = Hash::get($config, 'active');
        if (!is_bool($active)) {
            throw new InvalidArgumentException('Feature active status must be a boolean.');
        }

        $this->name = $name;
        $this->active = $active;
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
