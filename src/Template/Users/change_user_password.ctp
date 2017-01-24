<?php
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

$username = '';
if (!empty($this->passedArgs[0])) {
    $entity = TableRegistry::get('Users')->get($this->passedArgs[0]);
    $username = $entity->name;
}
?>
<section class="content-header">
    <h1>Change User Password</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-solid">
                <?php if (!empty($username)) : ?>
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Please enter the new password for user {0}', '<i>' . $username . '</i>') ?></h3>
                </div>
                <?php endif; ?>
                <?= $this->Form->create('User') ?>
                <div class="box-body">
                    <?= $this->Form->input('password'); ?>
                    <?= $this->Form->input('password_confirm', ['type' => 'password', 'required' => true]); ?>
                    <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section>
