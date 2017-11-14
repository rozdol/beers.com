<?php
namespace App\Feature;

use App\Feature\Feature;

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
            $config = new Config($params);

            $this->items[$config->getName()] = $config;
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
     * @param \App\Feature\Feature $name Feature name enum
     * @return \App\Feature\Config|null
     */
    public function get(Feature $name)
    {
        if (!array_key_exists($name->getValue(), $this->items)) {
            return null;
        }

        return $this->items[$name->getValue()];
    }
}
