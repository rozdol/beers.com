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
                    <?php if ($validatePassword) : ?>
                        <?= $this->Form->input('Users.current_password', [
                            'type' => 'password',
                            'required' => true,
                            'placeholder' => __('Current Password')
                        ]); ?>
                    <?php endif; ?>
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