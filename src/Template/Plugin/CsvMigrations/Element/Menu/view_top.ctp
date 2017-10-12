<?php
use CsvMigrations\FieldHandlers\FieldHandlerFactory;

$factory = new FieldHandlerFactory($this);

$plugin = $this->plugin;
$controller = $this->name;

$tableName = $controller;
if (!empty($plugin)) {
    $tableName = $plugin . '.' . $tableName;
}

$menu = [];

$url = ['plugin' => $plugin, 'controller' => $controller, 'action' => 'managePermissions'];
$menu[] = [
    'url' => $url,
    'label' => __('Permissions'),
    'icon' => 'shield',
    'type' => 'link_button_modal',
    'modal_target' => 'permissions-modal-add',
    'order' => 10,
    'raw_html' => $this->element('modal-permissions', ['id' => $this->request->param('pass.0')])
];

$url = ['plugin' => $plugin, 'controller' => $controller, 'action' => 'changelog', $options['entity']->id];
$menu[] = ['url' => $url, 'label' => __('Changelog'), 'icon' => 'book', 'type' => 'link_button', 'order' => 20];

$url = ['plugin' => $plugin, 'controller' => $controller, 'action' => 'edit', $options['entity']->id];
$menu[] = ['url' => $url, 'label' => __('Edit'), 'icon' => 'pencil', 'type' => 'link_button', 'order' => 90];

$url = ['plugin' => $plugin, 'controller' => $controller, 'action' => 'delete', $options['entity']->id];
$menu[] = [
    'url' => $url,
    'label' => __('Delete'),
    'icon' => 'trash',
    'type' => 'postlink_button',
    'order' => 100,
    'confirmMsg' => __('Are you sure you want to delete {0}?', $factory->renderValue(
        $tableName,
        $displayField,
        $options['entity']->{$displayField},
        ['renderAs' => 'plain']
    ))
];

echo $this->element('menu-render', ['menu' => $menu, 'user' => $user]);
