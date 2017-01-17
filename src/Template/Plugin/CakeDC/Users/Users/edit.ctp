<?php use Cake\Core\Configure; ?>
<section class="content-header">
    <h1><?= __('Edit {0}', ['User']) ?></h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <?= $this->Form->create($Users) ?>
                <div class="box-body">
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
                <div class="box-footer">
                    <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
                    <?php if (Configure::read('Users.GoogleAuthenticator.login')) : ?>
                    <?= $this->Form->postLink(
                        __d('CakeDC/Users', 'Reset Google Authenticator Token'),
                        [
                            'plugin' => 'CakeDC/Users',
                            'controller' => 'Users',
                            'action' => 'resetGoogleAuthenticator',
                            $Users->id
                        ], [
                            'class' => 'btn btn-danger',
                            'confirm' => __d(
                                'CakeDC/Users', 'Are you sure you want to reset token for user "{0}"?',
                                $Users->username
                            )
                        ]
                    ); ?>
                    <?php endif; ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section>