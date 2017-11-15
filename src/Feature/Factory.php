<?php
namespace App\Feature;

use App\Feature\Config;
use Cake\Controller\Component\AuthComponent;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use InvalidArgumentException;
use RuntimeException;

class Factory
{
    const FEATURE_INTERFACE = 'App\\Feature\\FeatureInterface';
    const FEATURE_SUFFIX = 'Feature';

    protected static $initialized = false;

    protected static $defaultOptions = ['name' => 'Base', 'active' => true];

    /**
     * Features Collection.
     *
     * @var \App\Feature\Collection
     */
    protected static $collection;

    protected static $auth;

    protected static $request;

    /**
     * Initialize feature.
     *
     * @return void
     */
    public static function init(AuthComponent $auth, ServerRequest $request)
    {
        if (static::$initialized) {
            return;
        }
        // set factory as initialized.
        static::$initialized = true;

        static::$auth = $auth;
        static::$request = $request;

        $features = Configure::read('Features');

        foreach ($features as $feature => $options) {
            $config = static::getConfig($feature);
            $class = static::getFeatureClass($config);
            $feature = new $class($config);
            $feature->isActive() ? $feature->enable() : $feature->disable();
        }
    }

    /**
     * Get feature method.
     *
     * @param string $name Feature name
     * @return \App\Feature\FeatureInterface
     */
    public static function get($name)
    {
        if (!static::$initialized) {
            throw new RuntimeException('Feature Factory is not initialized.');
        }

        if (!is_string($name)) {
            throw new InvalidArgumentException('Feature name must be a string.');
        }

        $config = static::getConfig($name);
        $class = static::getFeatureClass($config);

        return new $class($config);
    }

    protected static function getConfig($feature)
    {
        $options = Configure::read('Features.' . $feature);

        if (!empty($options)) {
            $options['name'] = $feature;
        }

        if (empty($options)) {
            Log::notice('Feature [' . $feature . '] does not exist.');
            $options = static::$defaultOptions;
        }

        $options['auth'] = static::$auth;
        $options['request'] = static::$request;

        return new Config($options);
    }

    protected static function getFeatureClass(Config $config)
    {
        $class = __NAMESPACE__ . '\\Type\\' . $config->get('name') . static::FEATURE_SUFFIX;
        if (!class_exists($class)) {
            throw new RuntimeException(
                'Class [' . $class . '] does not exist.'
            );
        }

        if (!in_array(static::FEATURE_INTERFACE, class_implements($class))) {
            throw new RuntimeException(
                'Feature class [' . $class . '] does not implement [' . static::FEATURE_INTERFACE . '].'
            );
        }

        return $class;
    }
}
