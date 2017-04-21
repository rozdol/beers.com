<?= $this->cell(
    'Menu.Menu',
    [
        'name' => MENU_MAIN,
        'renderAs' => [
            'header' => '<li class="header">MAIN NAVIGATION</li>',
            'menuStart' => '<ul class="sidebar-menu">',
            'itemStart' => '<li class="treeview">',
            'itemEnd' => '</li>',
            'childMenuStart' => '<ul class="treeview-menu">',
            'item' => '<a href="%url%" target="%target%"><i class="fa fa-%icon%"></i> <span>%label%</span></a>',
            'itemWithChildren' => '<a href="%url%" target="%target%"><i class="fa fa-%icon%"></i> <span>%label%</span><i class="fa fa-angle-left pull-right"></i></a>',
        ]
    ]
);
?>