<?php
//
// PHP extensions
//
$extensions = get_loaded_extensions();
?>
<div class="box box-default">
    <div class="box-header with-border">
        <i class="fa fa-building"></i>
        <h3 class="box-title"><?= __('PHP Extensions') ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <p>There are currently <strong><?php echo count($extensions); ?> PHP extensions</strong> loaded.</p>
        <ul class="list-inline">
        <?php foreach ($extensions as $extension) : ?>
            <li><?= $extension ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>