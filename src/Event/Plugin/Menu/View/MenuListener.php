<?php
namespace App\Event\Plugin\Menu\View;

use App\Feature\Factory as FeatureFactory;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Exception;
use Menu\Event\EventName;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;
use Qobo\Utils\Utility;
use RolesCapabilities\CapabilityTrait;

class MenuListener implements EventListenerInterface
{
    use CapabilityTrait;

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            (string)EventName::GET_MENU_ITEMS() => 'getMenuItems',
            (string)EventName::MENU_BEFORE_RENDER() => 'beforeRender'
        ];
    }

    /**
     * Method that returns menu nested array based on provided menu name
     *
     * @param \Cake\Event\Event $event Event object
     * @param string $name Menu name
     * @param array $user Current user
     * @param bool $fullBaseUrl Flag for fullbase url on menu links
     * @param array $modules Modules to fetch menu items for
     * @return void
     */
    public function getMenuItems(Event $event, $name, array $user, $fullBaseUrl = false, array $modules = [])
    {
        // include dashboard links on default main menu.
        $withDashboards = empty($modules) && MENU_MAIN === $name;

        if (empty($modules)) {
            $modules = Utility::findDirs(Configure::readOrFail('CsvMigrations.modules.path'));
        }

        $links = $this->getLinks($modules, $name);
        // add dashboard links
        if ($withDashboards) {
            $links[] = $this->getDashboardMenuItem($user);
        }

        if (empty($links)) {
            return;
        }

        $links = $this->filterLinks($links);
        if (empty($links)) {
            return;
        }

        $event->setResult($links);
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
        if (empty($menu)) {
            return;
        }

        if (empty($user)) {
            return;
        }

        $event->setResult($this->checkItemsAccess($menu, $user));
    }

    /**
     * Menu links getter.
     *
     * @param array $modules Modules list
     * @param string $menuName Menu name
     * @return array
     */
    protected function getLinks(array $modules, $menuName)
    {
        if (empty($modules)) {
            return [];
        }

        $result = [];
        foreach ($modules as $module) {
            $feature = FeatureFactory::get('Module' . DS . $module);
            if (!$feature->isActive()) {
                continue;
            }

            $links = $this->getModuleLinks($module, $menuName);
            $result = array_merge($result, $links);
        }

        return $result;
    }

    /**
     * Module links getter.
     *
     * @param string $module Module name
     * @param string $menuName Menu name
     * @return array
     */
    protected function getModuleLinks($module, $menuName)
    {
        $moduleConfig = new ModuleConfig(ConfigType::MENUS(), $module);
        $config = json_decode(json_encode($moduleConfig->parse()), true);

        if (empty($config[$menuName])) {
            return [];
        }

        $result = [];
        foreach ($config[$menuName] as $item) {
            $result[] = $item;
        }

        return $result;
    }

    /**
     * Dashboard menu item getter.
     *
     * @param array $user Current user
     * @return array
     */
    protected function getDashboardMenuItem(array $user)
    {
        $result = [
            'label' => 'Dashboards',
            'url' => '#',
            'icon' => 'tachometer',
            'order' => 0,
            'children' => $this->getDashboardLinks($user)
        ];

        return $result;
    }

    /**
     * Get dashboard links.
     *
     * @param array $user Current user
     * @return array
     */
    protected function getDashboardLinks(array $user)
    {
        $table = TableRegistry::get('Search.Dashboards');
        $query = $table->getUserDashboards($user)->order(['modified' => 'DESC']);

        $result[] = [
            'label' => 'Create',
            'url' => '/search/dashboards/add',
            'icon' => 'plus',
            'order' => 999999999
        ];

        if ($query->isEmpty()) {
            return $result;
        }

        foreach ($query as $k => $entity) {
            $result[] = [
                'label' => $entity->get('name'),
                'url' => ['plugin' => 'Search', 'controller' => 'Dashboards', 'action' => 'view', $entity->get('id')],
                'icon' => 'tachometer',
                'order' => $k
            ];
        }

        return $result;
    }

    /**
     * Menu links filter method.
     *
     * @param array $links Menu links
     * @return array
     */
    protected function filterLinks(array $links)
    {
        // handle plugin links
        foreach ($links as $k => $link) {
            $links[$k] = $this->filterPluginLinks($link);
        }

        // handle placeholder links
        foreach ($links as $k => $link) {
            $links[$k] = $this->filterPlaceholderLinks($link);
        }

        // handle empty items
        foreach ($links as $k => $link) {
            if (empty($link)) {
                unset($links[$k]);
            }
        }

        return $links;
    }

    /**
     * Filters plugin links based on active status.
     *
     * @param array $item Menu item
     * @return array
     */
    protected function filterPluginLinks(array $item)
    {
        if (empty($item)) {
            return [];
        }

        if (!empty($item['children'])) {
            foreach ($item['children'] as $k => $child) {
                if (!empty($this->filterPluginLinks($child))) {
                    continue;
                }

                unset($item['children'][$k]);
            }
        }

        $url = $item['url'];
        $url = is_string($url) ? array_filter(explode('/', $url)) : $url;

        // definitely not a plugin route
        if (2 > count($url)) {
            return $item;
        }

        // remove keys
        $url = array_values($url);

        // get plugin name
        $name = Inflector::camelize(Inflector::underscore($url[0]));

        $feature = FeatureFactory::get('Plugin' . DS . $name);

        return $feature->isActive() ? $item : [];
    }

    /**
     * Filters out placeholder links if no child items are found.
     *
     * @param array $item Menu item
     * @return array
     */
    protected function filterPlaceholderLinks(array $item)
    {
        if (empty($item)) {
            return [];
        }

        if (!empty($item['children'])) {
            foreach ($item['children'] as $k => $child) {
                if (!empty($this->filterPlaceholderLinks($child))) {
                    continue;
                }

                unset($item['children'][$k]);
            }
        }

        $url = $item['url'];
        $url = is_array($url) ? implode('/', $url) : $url;

        // not a placeholder link
        if ('#' !== trim($url)) {
            return $item;
        }

        // has children
        if (!empty($item['children'])) {
            return $item;
        }

        return [];
    }

    /**
     * Method responsible for checking user access on menu items.
     *
     * @param array $menu Menu items
     * @param array $user User details
     * @return array
     */
    protected function checkItemsAccess(array $menu, array $user)
    {
        $result = [];
        foreach ($menu as $item) {
            // this is for label like menu items without a url or children
            if (empty($item['url']) && empty($item['children'])) {
                $result[] = $item;
                continue;
            }

            $result[] = current($this->checkItemAccess([$item], $user));
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
    protected function checkItemAccess(array $items, array $user)
    {
        foreach ($items as $k => &$item) {
            $url = $item['url'];

            $internal = $this->isInternalLink($item['url']);

            // access check on internal links
            if ($internal) {
                $url = $this->parseUrl($item['url']);

                if (!$this->_checkAccess($url, $user)) {
                    // remove url from parent item on access check fail
                    if (!empty($item['children'])) {
                        unset($item['url']);
                    } else { // remove item on access check fail
                        unset($items[$k]);
                    }
                }
            }

            // evaluate child items
            if (!empty($item['children'])) {
                $item['children'] = $this->checkItemAccess($item['children'], $user);
                if (empty($item['children']) && (empty($item['url']) || '#' === trim($item['url']))) {
                    unset($items[$k]);
                }
            }
        }

        return $items;
    }

    /**
     * Checks if provided URL is an internal link.
     *
     * @param array|string $url URL
     * @return bool
     */
    protected function isInternalLink($url)
    {
        if (!is_string($url)) {
            return true;
        }

        if (!preg_match('/http/i', $url)) {
            return true;
        }

        if (0 === strpos($url, Router::fullBaseUrl())) {
            return true;
        }

        return false;
    }

    /**
     * Parses menu item URL.
     *
     * @param array|string $url Menu item URL
     * @return array
     */
    protected function parseUrl($url)
    {
        if (!is_string($url)) {
            return $url;
        }

        $fullBaseUrl = Router::fullBaseUrl();

        // strip out full base URL from menu item's URL.
        if (false !== strpos($url, $fullBaseUrl)) {
            $url = str_replace($fullBaseUrl, '', $url);
        }

        return Router::parse($url);
    }
}
