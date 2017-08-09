<?php
namespace App\Event\View;

use Cake\Core\Exception\Exception;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Network\Exception\ForbiddenException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Menu\MenuBuilder\Menu;
use Menu\MenuBuilder\MenuItemFactory;
use RolesCapabilities\CapabilityTrait;

abstract class BaseMenuListener implements EventListenerInterface
{
    use CapabilityTrait;

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
}
