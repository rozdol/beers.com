<?php
namespace App\Shell;

use Cake\ORM\Entity;
use CakeDC\Users\Shell\UsersShell as BaseShell;

class UsersShell extends BaseShell
{
    /**
     * Return a username for the new user
     *
     * If the username is provided as an argument,
     * return that.  Otherwise, generate a unique
     * username for the super user.
     *
     * @return string
     */
    protected function getUsername()
    {
        if (!empty($this->params['username'])) {
            return $this->params['username'];
        }

        return $this->Users->generateUniqueUsername('superadmin');
    }

    /**
     * Return a password for the new user
     *
     * If the password is provided as an argument,
     * return that.  Otherwise, generate a random
     * password.
     *
     * @return string
     */
    protected function getPassword()
    {
        if (!empty($this->params['password'])) {
            return $this->params['password'];
        }

        return $this->_generateRandomPassword();
    }

    /**
     * Return an email for the new user
     *
     * If the email is provided as an argument,
     * return that.  Otherwise, generate an email
     * based on a given username.
     *
     * @param string $username Username
     * @return string
     */
    protected function getEmail($username)
    {
        if (!empty($this->params['email'])) {
            return $this->params['email'];
        }

        return $username . '@example.com';
    }

    /**
     * Print out user information
     *
     * @param \Cake\ORM\Entity $user User entity
     * @param string $password Plain text password
     * @return void
     */
    protected function printUserInfo(Entity $user, $password)
    {
        $this->out(__d('CakeDC/Users', 'Superuser added:'));
        $this->out(__d('CakeDC/Users', 'Id: {0}', $user->id));
        $this->out(__d('CakeDC/Users', 'Username: {0}', $user->username));
        $this->out(__d('CakeDC/Users', 'Email: {0}', $user->email));
        $this->out(__d('CakeDC/Users', 'Password: {0}', $password));
    }

    /**
     * Print out user errors
     *
     * @param \Cake\ORM\Entity $user User entity
     * @return void
     */
    protected function printUserErrors(Entity $user)
    {
        $this->out(__d('CakeDC/Users', 'Superuser could not be added:'));

        collection($user->errors())->each(function ($error, $field) {
            $this->out(__d('CakeDC/Users', 'Field: {0} Error: {1}', $field, implode(',', $error)));
        });
    }

    /**
     * {@inheritDoc}
     */
    public function addSuperuser()
    {
        $username = $this->getUsername();
        $password = $this->getPassword();
        $email = $this->getEmail($username);

        $user = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'active' => 1,
        ];

        $userEntity = $this->Users->newEntity($user);
        $userEntity->is_superuser = true;
        $userEntity->role = 'superuser';
        $savedUser = $this->Users->save($userEntity);
        if (!empty($savedUser)) {
            $this->printUserInfo($savedUser, $password);
        } else {
            $this->printUserErrors($userEntity);
        }
    }
}
