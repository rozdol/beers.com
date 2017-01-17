<?php
//
// Installed CakePHP plugins
//
$plugins = \Cake\Core\Plugin::loaded();
?>
<div class="box box-default">
    <div class="box-header with-border">
        <i class="fa fa-plug"></i>
        <h3 class="box-title"><?= __('CakePHP Plugins') ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <p>There are currently <strong><?php echo count($plugins); ?> plugins</strong> installed.</p>
        <ul class="list-inline">
        <?php foreach ($plugins as $plugin) : ?>
            <li><?= $plugin ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>