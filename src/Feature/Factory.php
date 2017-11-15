<?php
namespace App\Feature;

use App\Feature\Collection;
use Cake\Core\Configure;
use Cake\Log\Log;
use InvalidArgumentException;
use RuntimeException;

class Factory
{
    const FEATURE_INTERFACE = 'App\\Feature\\FeatureInterface';
    const FEATURE_SUFFIX = 'Feature';

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

        $collection = static::getCollection();

        $config = $collection->get($name);
        if (is_null($config)) {
            $config = $collection->get(Collection::DEFAULT_FEATURE);
        }

        $class = __NAMESPACE__ . '\\Type\\' . $config->getName() . static::FEATURE_SUFFIX;
        if (!class_exists($class)) {
            Log::notice('Feature class [' . $class . '] does not exist.');

            // fallback to default feature
            $class = __NAMESPACE__ . '\\Type\\' . Collection::DEFAULT_FEATURE . static::FEATURE_SUFFIX;
        }

        if (!in_array(static::FEATURE_INTERFACE, class_implements($class))) {
            throw new RuntimeException(
                'Feature class [' . $class . '] does not implement [' . static::FEATURE_INTERFACE . '].'
            );
        }

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
        if (is_null($name)) {
            $collection = static::getCollection();
            foreach ($collection->all() as $item) {
                static::execute($item->getName());
            }

            return;
        }

        $feature = static::create($name);
        $feature->isActive() ? $feature->enable() : $feature->disable();
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
