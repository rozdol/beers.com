<?php
namespace App\Feature;

use App\Feature\Collection;
use Cake\Core\Configure;
use Cake\Log\Log;
use InvalidArgumentException;
use RuntimeException;

class Factory
{
    const FEATURE_SUFFIX = 'Feature';
    const BASE_CLASS = 'App\\Feature\\AbstractFeature';
    const BASE_FEATURE = 'Base';

    /**
     * Features Collection.
     *
     * @var \App\Feature\Collection
     */
    protected static $collection;

    /**
     * Create method.
     *
     * @param string $name Feature name
     * @return \App\Feature\FeatureInterface
     */
    public static function create($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Feature name must be a string.');
        }

        $class = __NAMESPACE__ . '\\Type\\' . $name . static::FEATURE_SUFFIX;
        if (!class_exists($class)) {
            Log::notice('Feature class [' . $class . '] does not exist.');

            // fallback to base feature
            $name = static::BASE_FEATURE;
            $class = __NAMESPACE__ . '\\Type\\' . $name . static::FEATURE_SUFFIX;
        }

        if (!is_subclass_of($class, static::BASE_CLASS)) {
            throw new RuntimeException('Feature class [' . $class . '] does not extend [' . static::BASE_CLASS . '].');
        }

        $config = static::getCollection()->get($name);

        return new $class($config);
    }

    /**
     * Initialize feature.
     *
     * @param string|null $name Feature name
     * @return void
     */
    public static function execute($name = null)
    {
        $items = is_null($name) ? static::getCollection()->all() : [static::getCollection()->get($name)];

        // loop through all features collection and enable/disable accordingly.
        foreach ($items as $item) {
            $feature = Factory::create($item->getName());
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
