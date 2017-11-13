<?php
namespace App\Feature;

use InvalidArgumentException;

class Collection
{
    /**
     * Feature items collection.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Constructor method.
     *
     * @param array $data Config
     */
    public function __construct(array $data)
    {
        foreach ($data as $params) {
            $feature = new Config($params);

            $this->items[$feature->getName()] = $feature;
        }
    }

    /**
     * Collection items getter method.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Collection item getter method.
     *
     * @param string $name Feature name
     * @return \App\Feature\Feature|null
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->items)) {
            return null;
        }

        return $this->items[$name];
    }
}
