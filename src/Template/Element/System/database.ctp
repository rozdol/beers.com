<?php
use \Cake\Datasource\ConnectionManager;
use \Cake\ORM\TableRegistry;

//
// Statistics
//
$allTables = ConnectionManager::get('default')->schemaCollection()->listTables();
$skipTables = 0;
$tableStats = [];
foreach ($allTables as $table) {
    // Skip phinx database schema version tables
    if (preg_match('/phinxlog/', $table)) {
        $skipTables++;
        continue;
    }
    // Bypassing any CakePHP logic for permissions, pagination, and so on,
    // and executing raw query to get reliable data.
    $sth = ConnectionManager::get('default')->execute("SELECT COUNT(*) AS total FROM `$table`");
    $result = $sth->fetch('assoc');
    $tableStats[$table]['total'] = $result['total'];

    $tableInstance = TableRegistry::get($table);
    $tableStats[$table]['deleted'] = 0;
    if ($tableInstance->hasField('trashed')) {
        $sth = ConnectionManager::get('default')->execute("SELECT COUNT(*) AS deleted FROM `$table` WHERE `trashed` IS NOT NULL AND `trashed` <> '0000-00-00 00:00:00'");
        $result = $sth->fetch('assoc');
        $tableStats[$table]['deleted'] = $result['deleted'];
    }
}

function getProgressValue($progress, $total)
{
    $result = '0%';

    if (!$progress || !$total) {
        return $result;
    }

    $result = number_format(100 * $progress / $total, 0) . '%';

    return $result;
}
?>
<div class="row">
    <div class="col-md-3">
        <div class="info-box bg-blue">
            <span class="info-box-icon"><i class="fa fa-database"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total tables</span>
                <span class="info-box-number"><?php echo number_format(count($allTables)); ?></span>
                <div class="progress">
                    <div class="progress-bar" style="width: <?php echo getProgressValue($skipTables, count($allTables)); ?>"></div>
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
                                <div class="progress-bar" style="width: <?php echo getProgressValue($counts['deleted'], $counts['total']); ?>"></div>
                            </div>
                            <span class="progress-description">
                                <?php echo number_format($counts['deleted']); ?> deleted records (<?php echo getProgressValue($counts['deleted'], $counts['total']); ?>)
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
