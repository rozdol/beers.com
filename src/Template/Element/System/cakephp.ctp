<?php
$plugins = $this->SystemInfo->getCakePhpPlugins();
?>
<div class="row">
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-birthday-cake"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">CakePHP version</span>
                <span class="info-box-number"><?= $this->SystemInfo->getCakePhpVersion() ?></span>
            </div>
        </div>
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-plug"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">CakePHP plugins</span>
                <span class="info-box-number"><?= number_format(count($plugins)); ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <b>Loaded plugins:</b>
        <ul>
        <?php foreach ($plugins as $plugin) : ?>
            <li><?= $plugin ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>
