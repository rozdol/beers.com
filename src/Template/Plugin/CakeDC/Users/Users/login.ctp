<?php
use Cake\Core\Configure;

//$this->layout = 'QoboAdminPanel.plain';
?>
<div class="container">
    <div class="row">
        <div class="col-xs-offset-1 col-xs-10 col-sm-offset-2 col-sm-8 col-md-6 col-md-offset-3">
            <div class="users form well well-lg">
                <?= $this->Form->create() ?>
                <p class="text-center">
                    <?php
                        if ($this->elementExists('logo')) {
                            echo $this->element('logo');
                        }
                    ?>
                </p>
                <?= $this->Flash->render('auth') ?>
                <?= $this->Flash->render() ?>
                <fieldset>
                    <?= $this->Form->input('username', ['required' => true]) ?>
                    <?= $this->Form->input('password', ['required' => true]) ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            if (Configure::check('Users.RememberMe.active')) {
                                echo $this->Form->input(Configure::read('Users.Key.Data.rememberMe'), [
                                    'type' => 'checkbox',
                                    'label' => __d('Users', 'Remember me'),
                                    'checked' => 'checked'
                                ]);
                            }
                            ?>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox text-right">
                                <?php
                                $registrationActive = Configure::read('Users.Registration.active');
                                if ($registrationActive) {
                                    echo $this->Html->link(__d('users', 'Register'), ['action' => 'register']);
                                }
                                if (Configure::read('Users.Email.required')) {
                                    if ($registrationActive) {
                                        echo ' | ';
                                    }
                                    echo $this->Html->link(__d('users', 'Reset Password'), ['action' => 'requestResetPassword']);
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <?= implode(' ', $this->User->socialLoginList()); ?>
                <?= $this->Form->button(__d('Users', '<span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> Login'), ['class' => 'btn btn-primary']); ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
