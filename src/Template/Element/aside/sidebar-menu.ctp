<?= $this->cell('Menu.Menu', [
    'name' => MENU_MAIN,
    'user' => $user,
    'fullBaseUrl' => false,
    'renderer' => 'Menu\\MenuBuilder\\MainMenuRenderAdminLte'
]) ?>