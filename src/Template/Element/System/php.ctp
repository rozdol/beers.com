<?php
//
// PHP setup
//
$setup = [
    'PHP Version' => PHP_VERSION,
    'Server API' => PHP_SAPI,
    'PHP Server User' => get_current_user(),
    'PHP Binary' => PHP_BINARY,
    'PHP Configuration File' => php_ini_loaded_file(),
    'Memory Limit' => ini_get('memory_limit'),
    'Max Execution Time' => ini_get('max_execution_time') . ' seconds',
    'Upload Max Filesize' => $this->Number->toReadableSize(sizeToBytes(ini_get('upload_max_filesize'))),
    'Post Max Size' => $this->Number->toReadableSize(sizeToBytes(ini_get('post_max_size'))),
];

//
// PHP extensions
//
$extensions = get_loaded_extensions();
asort($extensions);
?>
<div class="row">
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-heart"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">PHP version</span>
                <span class="info-box-number"><?php echo PHP_VERSION; ?></span>
            </div>
        </div>
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-plug"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">PHP extensions</span>
                <span class="info-box-number"><?php echo number_format(count($extensions)); ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <dl class="dl-horizontal">
        <?php
            foreach ($setup as $name => $value) {
                print '<dt>' . $name . ':</dt><dd>' . $value . '</dd>';
            }
        ?>
        </dl>
    </div>
    <div class="col-md-5">
        <b>Loaded extensions:</b>
        <ul class="list-inline">
        <?php foreach ($extensions as $extension) : ?>
            <li><?= $extension ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>
