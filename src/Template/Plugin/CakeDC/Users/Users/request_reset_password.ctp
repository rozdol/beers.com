<?php $this->layout = 'AdminLTE/login'; ?>
<?= $this->Form->create('User') ?>
<?= $this->Flash->render('auth') ?>
<?= $this->Flash->render() ?>
<fieldset>
    <div class="form-group has-feedback">
        <?= $this->Form->input('reference', [
            'required' => true,
            'label' => false,
            'placeholder' => 'Username',
            'templates' => [
                'inputContainer' => '{{content}}'
            ]
        ]) ?>
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
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
