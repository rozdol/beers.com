<?php
use Menu\MenuBuilder\Menu;
use Menu\MenuBuilder\MenuItemFactory;
use RolesCapabilities\Access\AccessFactory;

$menuBuilder = new Menu();
$accessFactory = new AccessFactory();
if (!isset($menuType)) {
    $menuType = Menu::MENU_BUTTONS_TYPE;
}

foreach ($menu as $item) {
    if (!$accessFactory->hasAccess($item['url'], $user)) {
        continue;
    }

    $menuItem = MenuItemFactory::createMenuItem($item);
    $menuBuilder->addMenuItem($menuItem);
}

$renderClass = 'Menu\\MenuBuilder\\Menu' . ucfirst($menuType) . 'Render';

if (!class_exists($renderClass)) {
    throw new Exception('Menu render class [' . $renderClass . '] is not found!');
}

$render = new $renderClass($menuBuilder, $this);

echo $render->render();
