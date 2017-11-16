<?php
namespace App\Feature;

use App\Feature\Config;

interface FeatureInterface
{
    /**
     * Contructor method.
     *
     * @param \App\Feature\Config $config Feature Config instance
     */
    public function __construct(Config $config);

    /**
     * Feature status getter method.
     *
     * @return bool
     */
    public function isActive();

    /**
     * Feature disable method.
     *
     * @return void
     */
    public function enable();

    /**
     * Feature disable method.
     *
     * @return void
     */
    public function disable();
}
