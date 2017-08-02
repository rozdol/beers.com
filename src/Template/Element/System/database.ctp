<?php

$allTables = $this->SystemInfo->getAllTables();
list($skipTables, $tableStats) = $this->SystemInfo->getTableStats();
?>
<div class="row">
    <div class="col-md-3">
        <div class="info-box bg-blue">
            <span class="info-box-icon"><i class="fa fa-database"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total tables</span>
                <span class="info-box-number"><?php echo number_format(count($allTables)); ?></span>
                <div class="progress">
                    <div class="progress-bar" style="width: <?php echo $this->SystemInfo->getProgressValue($skipTables, count($allTables)); ?>"></div>
                </div>
                <span class="progress-description"><?php echo $skipTables; ?> system tables</span>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="row">
            <?php foreach ($tableStats as $table => $counts) : ?>
                <div class="col-md-6">
                    <div class="info-box bg-aqua">
                        <span class="info-box-icon"><i class="fa fa-table"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text"><?php echo $table; ?></span>
                            <span class="info-box-number"><?php echo number_format($counts['total']); ?> records</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?php echo $this->SystemInfo->getProgressValue($counts['deleted'], $counts['total']); ?>"></div>
                            </div>
                            <span class="progress-description">
                                <?php echo number_format($counts['deleted']); ?> deleted records (<?php echo $this->SystemInfo->getProgressValue($counts['deleted'], $counts['total']); ?>)
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
