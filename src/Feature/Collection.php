<?php
namespace App\Feature;

use Cake\Log\Log;

class Collection
{
    const DEFAULT_FEATURE = 'Base';

    /**
     * Feature items collection.
     *
     * @var array
     */
    protected $items = [];

    protected $defaults = [
        ['name' => 'Base', 'active' => true]
    ];

    /**
     * Constructor method.
     *
     * @param array $data Config
     */
    public function __construct(array $data)
    {
        $data = $this->normalize($data);

        foreach ($data as $params) {
            $config = new Config($params);

            $this->items[$config->getName()] = $config;
        }
    }

    /**
     * Normalize features configuration data.
     *
     * @param array $data Configuration data
     * @return array
     */
    protected function normalize(array $data)
    {
        $result = [];

        // add defaults
        foreach ($this->defaults as $row) {
            $result[$row['name']] = $row;
        }

        // merge/overwrite data with defaults
        foreach ($data as $row) {
            if (array_key_exists($row['name'], $result)) {
                continue;
            }
            $result[$row['name']] = $row;
        }

        return $result;
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
     * @return \App\Feature\Config|null
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->items)) {
            Log::notice('No configuration found for Feature [' . $name . ']');

            return $this->items[static::DEFAULT_FEATURE];
        }

        return $this->items[$name];
    }
}
