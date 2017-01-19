<?php if (!$validatePassword) : ?>
<?php $this->layout = 'AdminLTE/change-password'; ?>
<?= $this->Form->create('User') ?>
<?= $this->Flash->render('auth') ?>
<?= $this->Flash->render() ?>
<fieldset>
    <div class="form-group has-feedback">
        <?= $this->Form->input('Users.password', [
            'type' => 'password',
            'required' => true,
            'label' => false,
            'placeholder' => __('Password')
        ]); ?>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
    </div>
    <div class="form-group has-feedback">
        <?= $this->Form->input('Users.password_confirm', [
            'type' => 'password',
            'required' => true,
            'label' => false,
            'placeholder' => __('Password Confirm')
        ]); ?>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
    </div>
    <div class="row">
        <div class="col-xs-4">
            <?= $this->Form->button(
                '<span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> ' . __d('Users', 'Submit'),
                ['class' => 'btn btn-primary btn-block btn-flat']
            ); ?>
        </div>
    </div>
    <?= $this->Form->end() ?>
</fieldset>
<?php else : ?>
<section class="content-header">
    <h1>Change Password</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Please enter the new password') ?></h3>
                </div>
                <?= $this->Form->create(); ?>
                <div class="box-body">
                    <?= $this->Form->input('Users.current_password', [
                        'type' => 'password',
                        'required' => true,
                        'placeholder' => __('Current Password')
                    ]); ?>
                    <?= $this->Form->input('Users.password', [
                        'type' => 'password',
                        'required' => true,
                        'placeholder' => __('Password')
                    ]); ?>
                    <?= $this->Form->input('Users.password_confirm', [
                        'type' => 'password',
                        'required' => true,
                        'placeholder' => __('Password Confirm')
                    ]); ?>
                    <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>