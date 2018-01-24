<?php
use Cake\Filesystem\Folder;

$dir = new Folder(WWW_ROOT . '/img/login');
$images = $dir->find();

echo $this->Html->tag(
    'style',
    '.login-page {' . $this->Html->style(['background-image' => 'url(/img/login/' . $images[array_rand($images)] . ')']) . '}'
);
?>
<?= $this->Form->create('User') ?>
<fieldset>
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">
                <span class="fa fa-user"></span>
            </span>
            <?= $this->Form->input('reference', [
                'required' => true,
                'label' => false,
                'placeholder' => 'Username',
                'templates' => [
                    'inputContainer' => '{{content}}'
                ]
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-8 col-xs-offset-2 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
            <?= $this->Form->button(__d('Users', 'Submit'), ['class' => 'btn btn-primary btn-block']); ?>
        </div>
    </div>
</fieldset>
<?= $this->Form->end() ?>