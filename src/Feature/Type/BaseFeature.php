<?php
namespace App\Feature\Type;

use App\Feature\Config;
use App\Feature\FeatureInterface;

class BaseFeature implements FeatureInterface
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
        return (bool)$this->config->get('active');
    }

    /**
     * {@inheritDoc}
     */
    public function enable()
    {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function disable()
    {
        //
    }
}
