<?php
namespace App\Feature;

class Manager
{
    /**
     * Features collection.
     *
     * @var \App\Feature\Collection;
     */
    protected $collection;

    /**
     * Constructor method.
     *
     * @param \App\Feature\Collection $collection Features collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Feature enabled checker.
     *
     * @param string $name Feature name
     * @return bool
     */
    public function isEnabled($name)
    {
        $config = $this->collection->get($name);

        if (is_null($config)) {
            return true;
        }

        return (bool)$config->isActive();
    }
}
