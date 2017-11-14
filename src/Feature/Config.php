<?php
namespace App\Feature;

use App\Feature\Feature;
use Cake\Utility\Hash;
use InvalidArgumentException;

class Config
{
    /**
     * Feature name as ENUM.
     *
     * @var \App\Feature\Feature
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
        if (!$name instanceof Feature) {
            throw new InvalidArgumentException('Feature name must be an enum.');
        }

        $active = Hash::get($config, 'active');
        if (!is_bool($active)) {
            throw new InvalidArgumentException('Feature active status must be a boolean.');
        }

        $this->name = $name->getValue();
        $this->active = (bool)$active;
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
