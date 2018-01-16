<?php
$packages = $this->SystemInfo->getComposerPackages();
$count = count($packages);

// Words to match in composer libraries and the icon to use
// for display count
$matches = [
    'cakephp' => 'birthday-cake',
    'qobo' => 'copyright'
];
$matchWords = array_keys($matches);
$matchCounts = $this->SystemInfo->getComposerMatchCounts($packages, $matchWords);

// Info boxes go into separate columns.  The width of each column depends on the
// number of matched words we use, plus one for the total count of packages.
$columnWidth = (int)floor(12 / (count($matchWords) + 1));
?>
<div class="row">
    <div class="col-md-<?= $columnWidth ?>">
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-book"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Composer libraries</span>
                <span class="info-box-number"><?php echo number_format($count); ?></span>
            </div>
        </div>
    </div>
    <?php foreach ($matchCounts as $word => $count) : ?>
        <div class="col-md-<?= $columnWidth ?>">
            <div class="info-box">
                <span class="info-box-icon bg-blue"><i class="fa fa-<?= $matches[$word] ?>"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Match <?php echo $word; ?></span>
                    <span class="info-box-number"><?php echo number_format($count); ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="row">
    <div class="col-md-12">
        <?php if (0 < $count) : ?>
            <table class="table table-hover table-condensed table-vertical-align">
                <thead>
                    <tr>
                        <th width="20%">Name</th>
                        <th width="10%">Version</th>
                        <th width="10%">License</th>
                        <th width="60%">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($packages as $package) : ?>
                        <?php
                            $url = '';
                            if (!empty($package['homepage'])) {
                                $url = $package['homepage'];
                            }
                            if (empty($url) && !empty($package['source']['url'])) {
                                $url = $package['source']['url'];
                            }
                        ?>
                        <tr>
                            <td><?= empty($url) ? $package['name'] : $this->Html->link($package['name'], $url, ['target' => '_blank']); ?></td>
                            <td><?= $package['version'] ?></td>
                            <td><?= implode(', ', $package['license']); ?></td>
                            <td><?= empty($package['description']) ? '&nbsp;' : $package['description'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
