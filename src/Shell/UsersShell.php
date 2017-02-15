<?php
namespace App\Shell;

use CakeDC\Users\Shell\UsersShell as BaseShell;

class UsersShell extends BaseShell
{
    /**
     * {@inheritDoc}
     */
    public function addSuperuser()
    {
        $username = (empty($this->params['username']) ?
            $this->Users->generateUniqueUsername('superadmin') : $this->params['username']);
        $password = (empty($this->params['password']) ?
            $this->_generateRandomPassword() : $this->params['password']);
        $email = (empty($this->params['email']) ? $username . '@example.com' : $this->params['email']);
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
            $this->out(__d('CakeDC/Users', 'Superuser added:'));
            $this->out(__d('CakeDC/Users', 'Id: {0}', $savedUser->id));
            $this->out(__d('CakeDC/Users', 'Username: {0}', $username));
            $this->out(__d('CakeDC/Users', 'Email: {0}', $savedUser->email));
            $this->out(__d('CakeDC/Users', 'Password: {0}', $password));
        } else {
            $this->out(__d('CakeDC/Users', 'Superuser could not be added:'));

            collection($userEntity->errors())->each(function ($error, $field) {
                $this->out(__d('CakeDC/Users', 'Field: {0} Error: {1}', $field, implode(',', $error)));
            });
        }
    }
}
