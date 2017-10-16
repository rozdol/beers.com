<?php
use RolesCapabilities\Access\AccessFactory;

$accessFactory = new AccessFactory();

$url = ['plugin' => $this->plugin, 'controller' => $this->name, 'action' => 'exportSearch'];
if ($accessFactory->hasAccess($url, $user)) {
    $url[] = $id;
    $url[] = $name;
    echo $this->Html->link('<i class="fa fa-download"></i> ' . __('Export'), $url, [
        'class' => 'btn btn-default', 'escape' => false
    ]);
}
