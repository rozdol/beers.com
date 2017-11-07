<?php
$menu = [];

list($plugin, $controller) = pluginSplit($model);
$url = ['plugin' => $plugin, 'controller' => $controller, 'action' => 'view', $entity->get('id')];
$menu[] = ['url' => $url, 'label' => __('View'), 'icon' => 'eye', 'type' => 'link_button', 'order' => 10];

echo $this->element('menu-render', ['menu' => $menu, 'user' => $user, 'menuType' => 'actions']);
