<?php
//
// Server environment
//
$server = [
    'Operating System' => implode(' ', [
        php_uname('s'),
        php_uname('r'),
    ]),
    'Machine Type' => php_uname('m'),
];

// Number of CPUs
$cpuInfoFile = '/proc/cpuinfo';
if (is_file($cpuInfoFile) && is_readable($cpuInfoFile)) {
    $cpuInfoFile = file($cpuInfoFile);
    $cpus = preg_grep("/^processor/", $cpuInfoFile);
    $server['Number of CPUs'] = count($cpus);
}

// RAM
$memoryInfoFile = '/proc/meminfo';
if (is_file($memoryInfoFile) && is_readable($memoryInfoFile)) {
    $memoryInfoFile = file($memoryInfoFile);
    $totalMemory = preg_grep("/^MemTotal:/", $memoryInfoFile);
    list($key, $size, $unit) = preg_split('/\s+/', $totalMemory[0], 3);
    $server['Total RAM'] = number_format($size) . ' ' . $unit;
}
?>
<div class="row">
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-linux"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Operating System</span>
                <span class="info-box-number"><?php echo PHP_OS; ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <dl class="dl-horizontal">
        <?php
            foreach ($server as $name => $value) {
                print '<dt>' . $name . ':</dt><dd>' . $value . '</dd>';
            }
        ?>
        </dl>
    </div>
</div>
