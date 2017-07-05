<?php
use Cake\Core\Configure;

$this->layout = 'AdminLTE/login';
?>
<?= $this->Form->create() ?>
<fieldset>
    <div class="form-group has-feedback">
        <?= $this->Form->input('username', [
            'required' => true,
            'label' => false,
            'placeholder' => 'Username',
            'templates' => [
                'inputContainer' => '{{content}}'
            ]
        ]) ?>
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
    </div>
    <div class="form-group has-feedback">
        <?= $this->Form->input('password', [
            'required' => true,
            'label' => false,
            'placeholder' => 'Password',
            'templates' => [
                'inputContainer' => '{{content}}'
            ]
        ]) ?>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
    </div>
    <div class="row">
        <div class="col-xs-8">
            <div class="checkbox icheck">
                <?php
                if (Configure::read('Users.RememberMe.active')) {
                    echo $this->Form->input(Configure::read('Users.Key.Data.rememberMe'), [
                        'type' => 'checkbox',
                        'label' => ' ' . __d('Users', 'Remember Me'),
                        'templates' => [
                            'inputContainer' => '{{content}}'
                        ]
                    ]);
                }
                ?>
            </div>
        </div>
        <div class="col-xs-4">
            <?= $this->Form->button(
                '<span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> ' . __d('Users', 'Sign In'),
                ['class' => 'btn btn-primary btn-block btn-flat']
            ); ?>
        </div>
    </div>
</fieldset>
<?= implode(' ', $this->User->socialLoginList()); ?>
<?= $this->Form->end() ?>
<?php
if (Configure::read('Users.Email.required') && !(bool)Configure::read('Ldap.enabled')) {
    echo $this->Html->link(__d('users', 'I forgot my password'), ['action' => 'requestResetPassword']) . '<br />';
}
if (Configure::read('Users.Registration.active')) {
    echo $this->Html->link(__d('users', 'Register a new membership'), ['action' => 'register']);
}
?>