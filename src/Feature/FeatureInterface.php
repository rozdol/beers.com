<?php
namespace App\Feature;

interface FeatureInterface
{
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
