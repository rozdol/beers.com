<?php
$formOptions = [
    'class' => 'sidebar-form',
    'url' => [
        'plugin' => $this->request->plugin,
        'controller' => $this->request->controller,
        'action' => 'search'
    ]
];
$inputOptions = [
    'label' => false,
    'div' => false,
    'container' => false,
    'class' => 'form-control',
    'placeholder' => 'Search in ' . strtolower($this->name) . '...',
    'templates' => [
        'inputContainer' => '{{content}}'
    ]
];
?>
<?= $this->Form->create(null, $formOptions); ?>
<div class="input-group">
<?= $this->Form->input('criteria[query]', $inputOptions); ?>
    <span class="input-group-btn">
        <?= $this->Form->button('<i class="fa fa-search"></i>', ['class' => 'btn btn-flat']); ?>
    </span>
</div>
<?= $this->Form->end(); ?>
