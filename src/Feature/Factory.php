<?php
namespace App\Feature;

use App\Feature\Collection;
use App\Feature\Feature;
use Cake\Core\Configure;
use InvalidArgumentException;
use RuntimeException;

class Factory
{
    /**
     * Features Collection.
     *
     * @var \App\Feature\Collection
     */
    protected static $collection;

    /**
     * Create method.
     *
     * @param \App\Feature\Feature|string $name Feature name
     * @return \App\Feature\FeatureInterface|null
     */
    public static function create($name)
    {
        $collection = static::getCollection();

        $type = $name;
        if ($type instanceof Feature) {
            $type = $type->getValue();
        }

        $class = __NAMESPACE__ . '\\Type\\' . $type . 'Feature';
        if (!class_exists($class)) {
            return null;
        }

        $interface = __NAMESPACE__ . '\\FeatureInterface';
        if (!in_array($interface, class_implements($class))) {
            throw new RuntimeException('Feature class [' . $class . '] does not implement [' . $interface . '].');
        }

        if (is_string($name)) {
            $name = Feature::{strtoupper($name)}();
        }

        $config = $collection->get($name);

        return new $class($config);
    }

    /**
     * Initialize feature.
     *
     * @param \App\Feature\Feature|null $name Feature name
     * @return void
     */
    public static function execute(Feature $name = null)
    {
        $items = is_null($name) ? static::getCollection()->all() : [static::getCollection()->get($name)];

        // loop through all features collection and enable/disable accordingly.
        foreach ($items as $item) {
            $feature = Factory::create($item->getName());
            if (is_null($feature)) {
                continue;
            }
            $feature->isActive() ? $feature->enable() : $feature->disable();
        }
    }

    /**
     * Features Collection getter.
     *
     * @return \App\Feature\Collection
     */
    protected static function getCollection()
    {
        if (!static::$collection instanceof Collection) {
            static::$collection = new Collection(Configure::read('Features'));
        }

        return static::$collection;
    }
}
