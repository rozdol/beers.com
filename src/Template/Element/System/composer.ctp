<?php
$packages = $this->SystemInfo->getComposerPackages();
$count = count($packages);
$matchCounts = $this->SystemInfo->getComposerMatchCounts($packages);
?>
<div class="row">
     <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-book"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Composer libraries</span>
                <span class="info-box-number"><?php echo number_format($count); ?></span>
            </div>
        </div>
        <?php foreach ($matchCounts as $word => $count) : ?>
            <div class="info-box">
                <span class="info-box-icon bg-blue"><i class="fa fa-check-square"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Match <?php echo $word; ?></span>
                    <span class="info-box-number"><?php echo number_format($count); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="col-md-9">
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
