<?php
namespace App\Model\Entity;

use CakeDC\Users\Model\Entity\User as BaseUser;
use Cake\Core\Configure;

class User extends BaseUser
{
    /**
     * @var $_virtual - make virtual fields visible to export to JSON or array
     */
    protected $_virtual = ['name', 'image', 'image_src'];

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
     * Virtual field image_src accessor.
     *
     * @return string
     */
    protected function _getImageSrc()
    {
        if (Configure::read('Users.gravatar.active') && $this->get('email')) {
            return $this->getGravatar($this->get('email'));
        }

        return $this->get('image');
    }

    /**
     * Gravatar getter.
     *
     * @param string $email User email
     * @return string
     */
    private function getGravatar($email)
    {
        return sprintf('https://www.gravatar.com/avatar/%s?s=150&d=mm&r=g', md5(strtolower(trim($email))));
    }
}
