<?php
namespace App\Feature;

use InvalidArgumentException;
use RuntimeException;

class Factory
{
    /**
     * Create method.
     *
     * @param \App\Feature\Config $config Feature Config instance
     * @return \App\Feature\FeatureInterface
     */
    public static function create(Config $config)
    {
        $class = __NAMESPACE__ . '\\Type\\' . $config->getType() . 'Feature';

        $interface = __NAMESPACE__ . '\\FeatureInterface';

        if (!in_array($interface, class_implements($class))) {
            throw new RuntimeException('Feature class [' . $class . '] does not implement [' . $interface . '].');
        }

        if (!class_exists($class)) {
            throw new RuntimeException('Feature class [' . $class . '] does not exist.');
        }

        return new $class($config);
    }
}
