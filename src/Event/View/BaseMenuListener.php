<?php
namespace App\Event\View;

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
    public function beforeRenderFlatMenu(Event $event, array $menu, array $user, $type = 'buttons')
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

        if ($type == 'actions') {
            $renderClass = 'Menu\\MenuBuilder\\MenuActionsRender';
        } else {
            $renderClass = 'Menu\\MenuBuilder\\MenuButtonsRender';
        }

        $render = new $renderClass($menuBuilder, $event->subject());
        $event->result = $render->render();

        return;

        // if empty user try to get it from the SESSION
        if (empty($user) && isset($_SESSION)) {
            $user = Hash::get($_SESSION, 'Auth.user');
            // if user still empty add all menu items to Event result and return
            if (empty($user)) {
                foreach ($menu as $item) {
                    $event->result .= $item['html'] . ' ';
                }

                return;
            }
        }

        foreach ($menu as $item) {
            // this is for label like menu items without a url
            if (empty($item['url'])) {
                $event->result .= $item['html'] . ' ';
                continue;
            }

            try {
                if (!$this->_checkAccess($item['url'], $user)) {
                    continue;
                }
                $event->result .= $item['html'] . ' ';
            } catch (ForbiddenException $e) {
                // do nothing
            }
        }
    }
}
