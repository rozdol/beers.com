<?php
namespace App\Event\View;

use Cake\Event\Event;
use Cake\Network\Request;
use Cake\ORM\Entity;

class ViewMenuListener extends BaseMenuListener
{
    /**
     * Menu element name
     */
    const MENU_ELEMENT = 'Menu.menu';

    /**
     * Implemented Events
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Search.Dashboards.View.View.Menu.Top' => 'getDashboardsViewMenuTop',
            'Search.View.View.Menu.Actions' => 'getSearchResultsIndexMenuActions',
            'CsvMigrations.Associated.actionsMenu.beforeRender' => 'beforeRenderFlatMenu',
            'CsvMigrations.View.topMenu.beforeRender' => 'beforeRenderCsvMigrationsViewTopMenu',
            'CsvMigrations.Dblists.Index.topMenu.beforeRender' => 'beforeRenderFlatMenu',
            'CsvMigrations.Dblists.Index.actionsMenu.beforeRender' => 'beforeRenderFlatMenu',
            'CsvMigrations.DblistItems.Index.topMenu.beforeRender' => 'beforeRenderFlatMenu',
            'CsvMigrations.DblistItems.Index.actionsMenu.beforeRender' => 'beforeRenderFlatMenu',
        ];
    }
    /**
     * Method that adds elements to CsvMigrations view View top menu.
     *
     * @param  \Cake\Event\Event $event Event object
     * @param  array             $menu  Menu
     * @param  array             $user  User
     * @return void
     */
    public function beforeRenderCsvMigrationsViewTopMenu(Event $event, array $menu, array $user)
    {
        $url = [
            'plugin' => $event->subject()->plugin,
            'controller' => $event->subject()->name,
            'action' => 'changelog',
            $event->subject()->passedArgs[0]
        ];

        $html = $event->subject()->Html->link(
            '<i class="fa fa-book"></i>',
            $url,
            ['title' => __('Changelog'), 'escape' => false]
        );

        array_unshift($menu, [
            'html' => $html,
            'url' => $url
        ]);

        $this->beforeRenderFlatMenu($event, $menu, $user);
    }

    /**
     * Method that adds elements to Dashboards view View top menu.
     *
     * @param  Cake\Event\Event     $event   Event object
     * @param  Cake\Network\Request $request Request object
     * @param  Cake\ORM\Entity      $entity  Entity object
     * @return void
     */
    public function getDashboardsViewMenuTop(Event $event, Request $request, Entity $entity)
    {
        $urlEdit = [
            'plugin' => $request->plugin,
            'controller' => $request->controller,
            'action' => 'edit',
            $entity->id
        ];
        $btnEdit = ' ' . $event->subject()->Html->link(
            '<i class="fa fa-pencil"></i>',
            $urlEdit,
            ['escape' => false, 'title' => __('Edit')]
        );

        $urlDel = [
            'plugin' => $request->plugin,
            'controller' => $request->controller,
            'action' => 'delete',
            $entity->id
        ];
        $btnDel = ' ' . $event->subject()->Form->postLink(
            '<i class="fa fa-trash"></i>',
            $urlDel,
            [
                'confirm' => __('Are you sure you want to delete {0}?', $entity->name),
                'title' => __('Delete'),
                'escape' => false
            ]
        );

        $menu = [
            [
                'label' => $btnEdit,
                'url' => $urlEdit,
                'capabilities' => 'fromUrl'
            ],
            [
                'label' => $btnDel,
                'url' => $urlDel,
                'capabilities' => 'fromUrl'
            ]
        ];

        if ($event->subject()->elementExists(static::MENU_ELEMENT)) {
            $event->result .= $event->subject()->element(static::MENU_ELEMENT, ['menu' => $menu, 'renderAs' => 'provided']);
        } else {
            $event->result .= $btnEdit . $btnDel;
        }
    }

    /**
     * Method that adds elements to Search results index View actions menu.
     *
     * @param  \Cake\Event\Event      $event  Event object
     * @param  \Cake\ORM\Entity|array $entity Entity
     * @param  string                 $model  Model name
     * @return void
     */
    public function getSearchResultsIndexMenuActions(Event $event, $entity, $model)
    {
        if ($entity instanceof Entity) {
            $entity = $entity->toArray();
        }

        list($plugin, $controller) = pluginSplit($model);

        $btnView = $event->subject()->Html->link(
            '<i class="fa fa-eye"></i>',
            ['plugin' => $plugin, 'controller' => $controller, 'action' => 'view', $entity['id']],
            ['title' => __('View'), 'class' => 'btn btn-default btn-sm', 'escape' => false]
        );

        $menu = [
            [
                'label' => $btnView,
                'url' => [
                    'plugin' => $plugin,
                    'controller' => $controller,
                    'action' => 'view',
                    $entity['id']
                ],
                'capabilities' => 'fromUrl'
            ]
        ];

        if ($event->subject()->elementExists(static::MENU_ELEMENT)) {
            $event->result .= $event->subject()->element(static::MENU_ELEMENT, ['menu' => $menu, 'renderAs' => 'provided']);
        } else {
            $event->result .= $btnView;
        }
    }
}
