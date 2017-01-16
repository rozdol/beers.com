<?php
use Cake\Core\Configure;
?>
<div class="row">
    <div class="col-xs-12">
        <?= $this->Flash->render() ?>
    </div>
    <div class="col-xs-12">
        <?= $this->Form->create($Users) ?>
        <fieldset>
            <legend><?= __('Edit User') ?></legend>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">&nbsp;</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <?= $this->Form->input('username'); ?>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <?= $this->Form->input('email'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <?= $this->Form->input('first_name'); ?>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <?= $this->Form->input('last_name'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <?= $this->Form->input('active'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
                                <?= $this->Form->end() ?>
                                <?php if (Configure::read('Users.GoogleAuthenticator.login')) : ?>
                                <?= $this->Form->postLink(
                                    __d('CakeDC/Users', 'Reset Google Authenticator Token'), ['plugin' => 'CakeDC/Users', 'controller' => 'Users', 'action' => 'resetGoogleAuthenticator', $Users->id], [
                                        'class' => 'btn btn-danger',
                                        'confirm' => __d('CakeDC/Users', 'Are you sure you want to reset token for user "{0}"?', $Users->username)
                                    ]);
                                ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
        </fieldset>
    </div>
</div>
