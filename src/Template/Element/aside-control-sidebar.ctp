<aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
        <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane" id="control-sidebar-settings-tab">
            <h3 class="control-sidebar-heading">Settings</h3>
            <?= $this->element('Menu.menu', [
                'name' => MENU_TOP,
                'renderAs' => [
                    'menuStart' => '<ul class="control-sidebar-menu">',
                    'itemStart' => '<li>',
                    'itemEnd' => '</li>',
                    'item' => '<a href="%url%"><i class="menu-icon fa fa-%icon%"></i> <div class="menu-info"><h4 class="control-sidebar-subheading">%label%</h4><p>%desc%</p></div></a>'
                ]
            ]);
            ?>
        </div>
    </div>
</aside>