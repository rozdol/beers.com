<?= $this->Form->create('User') ?>
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
</fieldset>
<?= $this->Form->end() ?>