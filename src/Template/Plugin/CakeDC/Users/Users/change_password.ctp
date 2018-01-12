<?php if (!$validatePassword) : ?>
<?php $this->layout = 'AdminLTE/login'; ?>
<?= $this->Form->create('User') ?>
<?= $this->Flash->render('auth') ?>
<?= $this->Flash->render() ?>
<fieldset>
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">
                <span class="fa fa-lock"></span>
            </span>
            <?= $this->Form->input('Users.password', [
                'type' => 'password',
                'required' => true,
                'label' => false,
                'placeholder' => __('Password')
            ]); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">
                <span class="fa fa-lock"></span>
            </span>
            <?= $this->Form->input('Users.password_confirm', [
                'type' => 'password',
                'required' => true,
                'label' => false,
                'placeholder' => __('Password Confirm')
            ]); ?>
        </div>
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
            <div class="box box-solid">
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