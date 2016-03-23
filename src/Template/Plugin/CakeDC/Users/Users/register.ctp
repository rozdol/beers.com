<?php
use Cake\Core\Configure;
?>
<div class="row">
    <div class="col-xs-12">
        <?= $this->Form->create($user); ?>
        <fieldset>
            <legend><?= __('Register'); ?></legend>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">&nbsp;</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <?= $this->Form->input('username'); ?>
                        </div>
                        <div class="col-xs-6">
                            <?= $this->Form->input('email'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <?= $this->Form->input('password'); ?>
                        </div>
                        <div class="col-xs-6">
                            <?= $this->Form->input('password_confirm', ['type' => 'password']); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <?= $this->Form->input('first_name'); ?>
                        </div>
                        <div class="col-xs-6">
                            <?= $this->Form->input('last_name'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <?php
                            if (Configure::read('Users.Tos.required')) {
                                echo $this->Form->input('tos', [
                                    'type' => 'checkbox',
                                    'label' => __d('Users', 'Accept TOS conditions?'),
                                    'required' => true
                                ]);
                            } else {
                                echo '&nbsp;';
                            }
                            ?>
                        </div>
                        <div class="col-xs-6">
                            <?= $this->User->addReCaptcha(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
        <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
