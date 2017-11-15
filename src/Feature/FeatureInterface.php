<?php
namespace App\Feature;

interface FeatureInterface
{
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
