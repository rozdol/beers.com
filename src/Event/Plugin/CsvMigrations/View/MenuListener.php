<?php
namespace App\Event\Plugin\CsvMigrations\View;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use CsvMigrations\Event\EventName;
use Menu\MenuBuilder\Menu;
use Menu\MenuBuilder\MenuItemFactory;
use RolesCapabilities\CapabilityTrait;

class MenuListener implements EventListenerInterface
{
    use CapabilityTrait;

    /**
     * Implemented Events
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            (string)EventName::MENU_ACTIONS_ASSOCIATED() => 'beforeRenderFlatMenu',
            (string)EventName::MENU_ACTIONS_DB_LISTS_INDEX() => 'beforeRenderFlatMenu',
            (string)EventName::MENU_ACTIONS_DB_LIST_ITEMS_INDEX() => 'beforeRenderFlatMenu',
            (string)EventName::MENU_ACTIONS_INDEX() => 'beforeRenderFlatMenu',
            (string)EventName::MENU_TOP_DB_LISTS_INDEX() => 'beforeRenderFlatMenu',
            (string)EventName::MENU_TOP_DB_LIST_ITEMS_INDEX() => 'beforeRenderFlatMenu',
            (string)EventName::MENU_TOP_INDEX() => 'beforeRenderFlatMenu',
            (string)EventName::MENU_TOP_VIEW() => 'beforeRenderCsvMigrationsViewTopMenu'
        ];
    }

    /**
     * Method that does acl check on flat (single level) menu items.
     *
     * @param  \Cake\Event\Event $event Event object
     * @param  array             $menu  Menu
     * @param  array             $user  User
     * @param  string            $type  menu type
     * @return void
     */
    public function beforeRenderFlatMenu(Event $event, array $menu, array $user, $type = Menu::MENU_BUTTONS_TYPE)
    {
        if (empty($menu)) {
            return;
        }
        $menuBuilder = new Menu();
        foreach ($menu as $item) {
            if (!empty($user) && !$this->_checkAccess($item['url'], $user)) {
                continue;
            }

            $menuItem = MenuItemFactory::createMenuItem($item);
            $menuBuilder->addMenuItem($menuItem);
        }

        $renderClass = 'Menu\\MenuBuilder\\Menu' . ucfirst($type) . 'Render';

        if (!class_exists($renderClass)) {
            throw new Exception('Menu render class [' . $renderClass . '] is not found!');
        }

        $render = new $renderClass($menuBuilder, $event->subject());
        $event->result = $render->render();
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
            '<i class="fa fa-book"></i> ' . __('Changelog'),
            $url,
            ['title' => __('Changelog'), 'escape' => false, 'class' => 'btn btn-default']
        );

        array_unshift($menu, [
            'html' => $html,
            'url' => $url
        ]);

        $this->beforeRenderFlatMenu($event, $menu, $user);
    }
}
