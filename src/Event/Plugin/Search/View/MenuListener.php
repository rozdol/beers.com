<?php
namespace App\Event\Plugin\Search\View;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Network\Request;
use Cake\ORM\Entity;
use Search\Event\EventName;

class MenuListener implements EventListenerInterface
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
            (string)EventName::MENU_TOP_DASHBOARD_VIEW() => 'getDashboardsViewMenuTop',
            (string)EventName::MENU_ACTIONS_SEARCH_VIEW() => 'getSearchResultsIndexMenuActions',
        ];
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
            '<i class="fa fa-pencil"></i> ' . __('Edit'),
            $urlEdit,
            ['escape' => false, 'title' => __('Edit'), 'class' => 'btn btn-default']
        );

        $urlDel = [
            'plugin' => $request->plugin,
            'controller' => $request->controller,
            'action' => 'delete',
            $entity->id
        ];
        $btnDel = ' ' . $event->subject()->Form->postLink(
            '<i class="fa fa-trash"></i> ' . __('Delete'),
            $urlDel,
            [
                'confirm' => __('Are you sure you want to delete {0}?', $entity->name),
                'title' => __('Delete'),
                'escape' => false,
                'class' => 'btn btn-default'
            ]
        );

        $menu = [
            [
                'label' => $btnEdit,
                'url' => $urlEdit,
                'capabilities' => 'fromUrl',
                'icon' => 'pencil',
                'type' => 'link_button',
            ],
            [
                'label' => $btnDel,
                'url' => $urlDel,
                'capabilities' => 'fromUrl',
                'icon' => 'trash',
                'type' => 'link_button',
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
            '<i class="fa fa-eye"></i> ',
            ['plugin' => $plugin, 'controller' => $controller, 'action' => 'view', $entity['id']],
            ['title' => __('View'), 'class' => 'btn btn-default', 'escape' => false]
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
