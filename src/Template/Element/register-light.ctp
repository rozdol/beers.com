<?php use Cake\Core\Configure; ?>
<?= $this->Form->create($user); ?>
<fieldset>
    <?= $this->Form->input('username'); ?>
    <?= $this->Form->input('email'); ?>
    <?= $this->Form->input('password'); ?>
    <?= $this->Form->input('password_confirm', ['type' => 'password']); ?>
    <?= $this->Form->input('first_name'); ?>
    <?= $this->Form->input('last_name'); ?>
    <?php if (!Configure::read('Users.Tos.required')) : ?>
        <div class="form-group">
        <?php
            $label = $this->Form->label('tos', __d('Users', 'Accept TOS conditions?'));
            echo $this->Form->input('tos', [
                'type' => 'checkbox',
                'class' => 'square',
                'required' => true,
                'label' => false,
                'templates' => [
                    'inputContainer' => '<div class="{{required}}">' . $label . '<div class="clearfix"></div>{{content}}</div>'
                ]
            ]);
        ?>
        </div>
    <?php endif; ?>
    <?php
    if (Configure::read('Users.Registration.reCaptcha') && Configure::read('Users.reCaptcha.registration')) {
        echo $this->User->addReCaptcha();
    }
    ?>
</fieldset>
<?= $this->Form->button(__('Register'), ['class' => 'btn btn-primary btn-block btn-flat']) ?>
<?= $this->Form->end() ?>