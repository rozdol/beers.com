<?php
use Cake\Utility\Inflector;

$elements = [
    'Notes' => [
        'element' => 'notes',
        'id' => 'notes',
        'label' => 'Notes',
        'icon' => 'sticky-note'
    ]
];

/**
 * @todo make sidebar better and more dynamic
 */
if ('view' !== $this->request->params['action'] || 'Notes' === $this->request->params['controller'] || 'Search' === $this->request->plugin) {
$elements = [];
}

foreach ($elements as $plugin => $element) {
    if (!$this->elementExists($plugin . '.' . $element['element'])) {
        unset($elements[$plugin]);
    }
}
?>
<aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
        <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
        <?php if (!empty($elements)) : ?>
            <?php foreach ($elements as $element) : ?>
            <li>
                <a href="#control-sidebar-<?= $element['id'] ?>-tab" data-toggle="tab">
                    <i class="fa fa-<?= $element['icon'] ?>"></i>
                </a>
            </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Home tab content -->
        <div class="tab-pane active" id="control-sidebar-home-tab">
        </div>
        <!-- /.tab-pane -->

        <!-- Settings tab content -->
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
        <!-- /.tab-pane -->
        <?php if (!empty($elements)) : ?>
            <?php foreach ($elements as $plugin => $element) : ?>
            <!-- <?= $plugin ?> tab content -->
            <div class="tab-pane active" id="control-sidebar-<?= $element['id'] ?>-tab">
                <h3 class="control-sidebar-heading"><?= $element['label'] ?></h3>
                <?= $this->element($plugin . '.' . $element['element']); ?>
            </div>
            <!-- /.tab-pane -->
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</aside>