<?php
use Cake\Core\Configure;

$menu = [];

list($plugin, $controller) = pluginSplit($model);

$url = ['prefix' => false, 'plugin' => $plugin, 'controller' => $controller, 'action' => 'view', $entity->get('id')];
$menu[] = ['url' => $url, 'icon' => 'eye', 'label' => __('View'), 'type' => 'link_button', 'order' => 10];

$url = ['prefix' => false, 'plugin' => $plugin, 'controller' => $controller, 'action' => 'edit', $entity->get('id')];
$menu[] = ['url' => $url, 'icon' => 'pencil', 'label' => __('Edit'), 'type' => 'link_button', 'order' => 20];

$url = ['prefix' => 'api', 'plugin' => $plugin, 'controller' => $controller, 'action' => 'delete', '_ext' => 'json', $entity->get('id')];
$menu[] = [
    'url' => $url,
    'icon' => 'trash',
    'label' => __('Delete'),
    'type' => 'link_button',
    'confirmMsg' => 'Are you sure you want to delete this record?',
    'order' => 30
];

echo $this->element('menu-render', ['menu' => $menu, 'user' => $user, 'menuType' => 'actions']);
echo $this->Html->scriptBlock('
    // trigger deletion of the record from the dynamic DataTables entries
    $("a[href=\'' . $this->Url->build($url) . '\']").click(function (e) {
        e.preventDefault();

        var that = this;

        if (! confirm($(this).data("confirm-msg"))) {
            return;
        }

        $.ajax({
            url: $(this).attr("href"),
            method: "DELETE",
            dataType: "json",
            contentType: "application/json",
            headers: {
                Authorization: "Bearer ' . Configure::read("API.token") . '"
            },
            success: function (data) {
                // traverse upwards on the tree to find table instance and reload it
                $(that).closest("table").DataTable().ajax.reload();
            }
        });
    });
');
