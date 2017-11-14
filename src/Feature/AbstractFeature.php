<?php
namespace App\Feature;

use App\Feature\FeatureInterface;

abstract class AbstractFeature implements FeatureInterface
{
    /**
     * Feature config
     *
     * @var \App\Feature\Config
     */
    protected $config;

    /**
     * Contructor method.
     *
     * @param \App\Feature\Config $config Feature Config instance
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function isActive()
    {
        return (bool)$this->config->isActive();
    }

    /**
     * {@inheritDoc}
     */
    public function enable()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function disable()
    {
    }
}
