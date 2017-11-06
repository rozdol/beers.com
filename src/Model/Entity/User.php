<?php
namespace App\Model\Entity;

use CakeDC\Users\Model\Entity\User as BaseUser;

class User extends BaseUser
{
    /**
     * @var $_virtual - make virtual fields visible to export to JSON or array
     */
    protected $_virtual = ['name', 'image'];

    /**
     * Virtual Field: name
     *
     * Try to use first name and last name together, but
     * if it produces an empty result, fallback onto the
     * username.
     *
     * @return string
     */
    protected function _getName()
    {
        $result = trim($this->first_name . ' ' . $this->last_name);
        if (empty($result)) {
            $result = $this->username;
        }

        return $result;
    }

    /**
     * User image accessor
     *
     * Converts image resource into a string.
     *
     * @param resource $image Image resource
     * @return string
     */
    protected function _getImage($image)
    {
        if (is_resource($image)) {
            return stream_get_contents($image);
        }

        return $image;
    }
}
