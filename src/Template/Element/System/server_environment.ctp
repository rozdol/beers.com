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
<h4>Server Environment</h4>
<ul>
<?php
    foreach ($server as $name => $value) {
        print '<li><b>' . $name . ':</b> ' . $value . '</li>';
    }
?>
</ul>
