<?php
//
// Installed composer libraries (from composer.lock file)
//
$composerLock = ROOT . DS . 'composer.lock';
$composer = null;
if (is_readable($composerLock)) {
    $composer = json_decode(file_get_contents($composerLock), true);
}
$packages = !empty($composer['packages']) ? $composer['packages'] : [];
$count = count($packages);
?>
<div class="box box-default">
    <div class="box-header with-border">
        <i class="fa fa-th-list"></i>
        <h3 class="box-title"><?= __('Composer Libraries') ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <p>There are currently <strong><?= $count ?> composer libraries</strong> installed.</p>
        <?php if (0 < $count) : ?>
            <table class="table table-hover table-condensed table-vertical-align">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Version</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($packages as $package) : ?>
                        <tr>
                            <td><?= $package['name'] ?></td>
                            <td><?= $package['version'] ?></td>
                            <td><?= empty($package['description']) ? '&nbsp;' : $package['description'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>