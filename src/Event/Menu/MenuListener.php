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
            'Menu.Menu.getMenu' => 'getMenu',
            'Menu.Menu.beforeRender' => 'beforeRender'
        ];
    }

    /**
     * Method that returns menu nested array based on provided menu name
     *
     * @param \Cake\Event\Event $event Event object
     * @param string $name Menu name
     * @param array $user Current user
     * @param bool $fullBaseUrl Flag for fullbase url on menu links
     * @return void
     */
    public function getMenu(Event $event, $name, array $user, $fullBaseUrl)
    {
        $menus = [
            'sidebar' => [
                [
                    'label' => 'Dashboards',
                    'url' => '/search/dashboards/',
                    'icon' => 'tachometer',
                    'children' => $this->_getDashboardLinks($user)
                ]
            ],
            'top' => [
                ['label' => 'Users', 'desc' => 'Manage system users', 'url' => '/users/', 'icon' => 'user bg-yellow'],
                ['label' => 'Groups', 'desc' => 'Manage system groups', 'url' => '/groups/groups/', 'icon' => 'users bg-orange'],
                ['label' => 'Roles', 'desc' => 'Manage system roles', 'url' => '/roles-capabilities/Roles/', 'icon' => 'unlock bg-green'],
                ['label' => 'Lists', 'desc' => 'Manage database lists', 'url' => '/csv-migrations/dblists/', 'icon' => 'list bg-blue'],
                ['label' => 'Logs', 'desc' => 'View system logs', 'url' => '/Logs/', 'icon' => 'list-alt bg-red'],
                ['label' => 'Information', 'desc' => 'System information screen', 'url' => '/System/info', 'icon' => 'info-circle bg-light-blue'],
                ['label' => 'Settings', 'desc' => 'System settings', 'url' => '#', 'icon' => 'cog bg-olive']
            ]
        ];

        if (empty($menus[$name])) {
            return;
        }

        if ((bool)$fullBaseUrl) {
            $menus[$name] = $event->subject()->Menu->setFullBaseUrl($menus[$name]);
        }

        $event->result = $menus[$name];
    }

    /**
     * Get dashboard links for the menu.
     *
     * @param array $user Current user
     * @return array
     */
    protected function _getDashboardLinks(array $user)
    {
        $dashboards = TableRegistry::get('Search.Dashboards')->getUserDashboards($user);

        $result = [];
        foreach ($dashboards as $dashboard) {
            $result[] = [
                'label' => $dashboard->name,
                'url' => [
                    'plugin' => 'Search',
                    'controller' => 'Dashboards',
                    'action' => 'view',
                    $dashboard->id
                ],
                'icon' => 'tachometer'
            ];
        }

        $result[] = [
            'label' => 'Create',
            'url' => '/search/dashboards/add',
            'icon' => 'plus'
        ];

        return $result;
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
        $fullBaseUrl = Router::fullBaseUrl();
        foreach ($items as $k => &$item) {
            $url = $item['url'];
            if (is_string($url)) {
                // strip out full base URL if is part of menu item's URL
                $url = false !== strpos($url, $fullBaseUrl) ? str_replace($fullBaseUrl, '', $url) : $url;
                $url = Router::parse($url);
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
