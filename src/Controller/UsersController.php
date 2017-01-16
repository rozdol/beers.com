<?php
namespace App\Controller;

use App\Controller\AppController;
use CakeDC\Users\Controller\Component\UsersAuthComponent;
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

    public function changeUserPassword($id = null)
    {
        $user = $this->getUsersTable()->newEntity();
        $user->id = empty($id) ? $this->Auth->user('id') : $id;
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
}
