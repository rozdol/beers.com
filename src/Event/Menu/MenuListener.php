<?php
namespace App\Event\Menu;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Network\Exception\ForbiddenException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

class MenuListener implements EventListenerInterface
{
    /**
     * ACL instance
     *
     * @var object
     */
    protected $_aclInstance;

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            'Menu.Menu.beforeRender' => 'beforeRender'
        ];
    }

    /**
     * Method that adds elements to view View top menu.
     *
     * @param  \Cake\Event\Event $event Event object
     * @param  array             $menu  Menu
     * @param  array             $user  User
     * @return void
     */
    public function beforeRender(Event $event, array $menu, array $user)
    {
        $event->result = $this->_checkItemsAccess($event, $menu, $user);
    }

    /**
     * Method responsible for checking user access on menu items.
     *
     * @param  \Cake\Event\Event $event Event object
     * @param  array             $menu  Menu items
     * @param  array             $user  User details
     * @return array
     */
    protected function _checkItemsAccess(Event $event, array $menu, array $user)
    {
        $result = [];
        foreach ($menu as $item) {
            // this is for label like menu items without a url or children
            if (empty($item['url']) && empty($item['children'])) {
                $result[] = $item;
                continue;
            }

            // if empty user get it from the SESSION
            if (empty($user)) {
                if (!empty($_SESSION['Auth']['User'])) {
                    $user = $_SESSION['Auth']['User'];
                }
            }

            // skip on empty user
            if (empty($user)) {
                $result[] = $item;
                continue;
            }

            $this->_aclInstance = TableRegistry::get('RolesCapabilities.Capabilities');

            $result[] = current($this->_checkItemAccess([$item], $user));
        }

        return $result;
    }

    /**
     * Method responsible for checking user access on menu current item(s).
     *
     * @param  array  $items Menu current item(s)
     * @param  array  $user  User details
     * @return array
     */
    protected function _checkItemAccess(array $items, array $user)
    {
        foreach ($items as $k => &$item) {
            if (is_string($item['url'])) {
                $url = Router::parse($item['url']);
            } else {
                $url = $item['url'];
            }

            try {
                $this->_aclInstance->checkAccess($url, $user);
            } catch (ForbiddenException $e) {
                if (!empty($item['children'])) {
                    // remove url from parent item on access check fail
                    unset($item['url']);
                } else {
                    // remove child item on access check fail
                    unset($items[$k]);
                }
            }

            // evaluate child items
            if (!empty($item['children'])) {
                $item['children'] = $this->_checkItemAccess($item['children'], $user);
                if (empty($item['children'])) {
                    $item = [];
                }
            }
        }

        return $items;
    }
}
