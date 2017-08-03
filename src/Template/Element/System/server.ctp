<?php

$server = $this->SystemInfo->getServerInfo();
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
