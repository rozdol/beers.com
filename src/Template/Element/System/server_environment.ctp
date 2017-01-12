<?php
//
// Server environment
//
$server = [
    'Server Operating System' => implode(' ', [
        php_uname('s'),
        php_uname('r'),
    ]),
    'Server Architecture' => php_uname('m'),
    'PHP Version' => PHP_VERSION,
    'PHP Server API' => PHP_SAPI,
    'PHP Server User' => get_current_user(),
    'PHP Binary' => PHP_BINARY,
    'PHP Configuration File' => php_ini_loaded_file(),
    'PHP Memory Limit' => ini_get('memory_limit'),
    'PHP Maximum Execution Time' => ini_get('max_execution_time') . ' seconds',
];
?>
<div class="box box-default">
    <div class="box-header with-border">
        <i class="fa fa-server"></i>
        <h3 class="box-title"><?= __('Server Environment') ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <ul>
        <?php
            foreach ($server as $name => $value) {
                print '<li><b>' . $name . ':</b> ' . $value . '</li>';
            }
        ?>
        </ul>
    </div>
</div>