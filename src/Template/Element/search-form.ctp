<?php
use Cake\Utility\Inflector;

$formOptions = [
    'class' => 'sidebar-form',
    'url' => [
        'plugin' => $this->request->plugin,
        'controller' => $this->request->controller,
        'action' => 'search'
    ]
];

if (!isset($name)) {
    $name = $this->name;
}

$name = Inflector::humanize(Inflector::underscore($name));

$inputOptions = [
    'label' => false,
    'div' => false,
    'container' => false,
    'class' => 'form-control',
    'placeholder' => 'Search in ' . strtolower($name) . '...',
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
