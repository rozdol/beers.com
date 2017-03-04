<?php
// Tabs
//
// Key is used as element name, href link and
// div id, so pick wisely.
$tabs = [
    'project' => [
        'label' => 'Project',
        'icon' => 'fa-info-circle',
    ],
    'cakephp' => [
        'label' => 'CakePHP',
        'icon' => 'fa-birthday-cake',
    ],
    'composer' => [
        'label' => 'Composer',
        'icon' => 'fa-book',
    ],
    'php' => [
        'label' => 'PHP',
        'icon' => 'fa-heart',
    ],
    'server' => [
        'label' => 'Server',
        'icon' => 'fa-linux',
    ],
    'database' => [
        'label' => 'Database',
        'icon' => 'fa-database',
    ],
    'developer' => [
        'label' => 'Developer',
        'icon' => 'fa-wrench',
    ],
];
?>
<section class="content-header">
    <h1><?= __('System Information'); ?></h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <?php
                        $first = true;
                        foreach ($tabs as $tab => $settings) {
                           echo $first ? '<li class="active">' : '<li>';
                           echo '<a href="#' . $tab . '" data-toggle="tab" aria-expanded="true">';
                           echo '<i class="fa ' . $settings['icon'] . '"></i>';
                           echo ' ';
                           echo $settings['label'];
                           echo '</a>';
                           echo '</li>';
                           $first = false;
                        }
                    ?>
                </ul>
                <div class="tab-content">
                    <?php
                        $first = true;
                        foreach ($tabs as $tab => $settings) {
                            echo $first ? '<div class="tab-pane active" id="' . $tab . '">' : '<div class="tab-pane" id="' . $tab . '">';
                            echo $this->element('System/' . $tab);
                            echo '</div>';
                            $first = false;
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
