<?php
namespace App\Avatar;

interface AvatarInterface
{

    /**
     * Constructor method.
     *
     * @param array $options Avatar options
     */
    public function __construct(array $options);

    /**
     * Avatar getter method.
     *
     * @return string
     */
    public function get();
}
