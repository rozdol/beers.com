<?php
namespace App\Avatar;

final class Service
{
    /**
     * @var \App\Avatar\AvatarInterface
     */
    private $avatar;

    /**
     * Constructor method.
     *
     * @param \App\Avatar\AvatarInterface $avatar Avatar instance
     * @return void
     */
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
