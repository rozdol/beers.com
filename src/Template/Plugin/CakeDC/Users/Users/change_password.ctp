<div class="row">
    <div class="col-xs-12">
        <?= $this->Flash->render('auth') ?>
        <?= $this->Form->create($user) ?>
        <fieldset>
            <legend><?= __('Please enter the new password') ?></legend>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">&nbsp;</h3>
                </div>
                <div class="panel-body">
                    <?php if ($validatePassword) : ?>
                    <div class="row">
                        <div class="col-xs-6">
                            <?= $this->Form->input('current_password', [
                                    'type' => 'password',
                                    'required' => true,
                                    'label' => __d('Users', 'Current password')]);
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-xs-6">
                            <?= $this->Form->input('password'); ?>
                        </div>
                        <div class="col-xs-6">
                            <?= $this->Form->input('password_confirm', ['type' => 'password', 'required' => true]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
        <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>