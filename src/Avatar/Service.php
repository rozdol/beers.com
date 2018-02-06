<?php
namespace App\Avatar;

final class Service
{
    private $avatar;

    public function __construct(AvatarInterface $avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * Fetches avatar image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->avatar->get();
    }
}
