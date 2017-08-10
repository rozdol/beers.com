<?php
namespace App\Controller;

use App\Controller\AppController;
use CakeDC\Users\Controller\Traits\CustomUsersTableTrait;
use CakeDC\Users\Exception\UserNotActiveException;
use CakeDC\Users\Exception\UserNotFoundException;
use CakeDC\Users\Exception\WrongPasswordException;
use Cake\Core\Configure;
use Cake\Validation\Validator;
use Exception;

/**
 * Users Controller
 */
class UsersController extends AppController
{
    use CustomUsersTableTrait;

    /**
     * changeUserPassword method
     *
     * change user passwords by the superusers
     *
     * @param mixed $id user id
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     */
    public function changeUserPassword($id)
    {
        $user = $this->getUsersTable()->newEntity();
        $user->id = $id;
        $redirect = ['plugin' => 'CakeDC/Users', 'controller' => 'Users', 'action' => 'index'];

        if ($this->request->is('post')) {
            try {
                $validator = $this->getUsersTable()->validationPasswordConfirm(new Validator());
                $user = $this->getUsersTable()->patchEntity($user, $this->request->data(), ['validate' => $validator]);

                if ($user->errors()) {
                    $this->Flash->error(__d('CakeDC/Users', 'Password could not be changed'));
                } else {
                    $user = $this->getUsersTable()->changePassword($user);
                    if ($user) {
                        $this->Flash->success(__d('CakeDC/Users', 'Password has been changed successfully'));

                        return $this->redirect($redirect);
                    } else {
                        $this->Flash->error(__d('CakeDC/Users', 'Password could not be changed'));
                    }
                }
            } catch (UserNotFoundException $exception) {
                $this->Flash->error(__d('CakeDC/Users', 'User was not found'));
            } catch (WrongPasswordException $wpe) {
                $this->Flash->error(__d('CakeDC/Users', '{0}', $wpe->getMessage()));
            } catch (Exception $exception) {
                $this->Flash->error(__d('CakeDC/Users', 'Password could not be changed'));
            }
        }

        $this->set(compact('user'));
    }

    /**
     * Upload user image
     *
     * Converts and stores user image in base64 scheme.
     *
     * @param string $id User id
     * @return \Cake\Network\Response
     */
    public function uploadImage($id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);

        $user = $this->Users->get($id);

        // user already has image flag
        $hasImage = $user->get('image');

        $data = $this->request->data('Users.image');

        if (!$data) {
            $this->Flash->error(__('Failed to upload image, please try again.'));

            return $this->redirect($this->request->referer());
        }

        if (524288 < $data['size']) {
            $this->Flash->error(__('Image is too large. Max size 512kb.'));

            return $this->redirect($this->request->referer());
        }

        list($mimeGroup, ) = explode('/', $data['type']);

        // show error and redirect if uploaded file is not an image
        if ('image' !== strtolower($mimeGroup)) {
            $this->Flash->error(__('Unsupported image type.'));

            return $this->redirect($this->request->referer());
        }

        // base64 encode image
        $image = 'data:' . $data['type'] . ';base64,' . base64_encode(file_get_contents($data['tmp_name']));

        $user = $this->Users->patchEntity($user, ['image' => $image]);

        if ($this->Users->save($user)) {
            if ($hasImage) {
                $this->Flash->success(__('The image has been replaced.'));
            } else {
                $this->Flash->success(__('The image has been uploaded.'));
            }
        } else {
            $this->Flash->error(__('Failed to upload image, please try again.'));
        }

        return $this->redirect($this->request->referer());
    }

    /**
     * editProfile method
     *
     *
     */
    public function editProfile()
    {
        $this->autoRender = false;
        $this->request->allowMethod(['patch', 'post', 'put']);

        $user_id = $this->Auth->user('id');

        $user = $this->Users->get($user_id);

        $user = $this->Users->patchEntity($user, $this->request->data);

        if ($this->Users->save($user)) {
            $this->Flash->success(__('Profile successfully updated'));
        } else {
            $this->Flash->error(__('Failed to update profile data, please try again.'));
        }

        return $this->redirect($this->request->referer());
    }
}
