<?php
namespace App\Feature;

use App\Feature\FeatureInterface;

abstract class AbstractFeature implements FeatureInterface
{
    /**
     * Contructor method.
     *
     * @param \App\Feature\Config $config Feature Config instance
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }
}
