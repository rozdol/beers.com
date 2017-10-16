<?php
$menu = [];

$url = ['plugin' => 'Search', 'controller' => 'Dashboards', 'action' => 'edit', $entity->get('id')];
$menu[] = ['url' => $url, 'label' => __('Edit'), 'icon' => 'pencil', 'type' => 'link_button', 'order' => 10];

$url = ['plugin' => 'Search', 'controller' => 'Dashboards', 'action' => 'delete', $entity->get('id')];
$menu[] = [
    'url' => $url,
    'label' => __('Delete'),
    'icon' => 'trash',
    'type' => 'postlink_button',
    'order' => 20,
    'confirmMsg' => __('Are you sure you want to delete {0}?', $entity->get('name'))
];

echo $this->element('menu-render', ['menu' => $menu, 'user' => $user]);
