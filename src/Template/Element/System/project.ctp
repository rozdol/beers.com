<?php
//
// Project information
//

?>
<div class="row">
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-info-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Project version</span>
                <span class="info-box-number"><?php echo $projectVersion; ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <dl class="dl-horizontal">
            <dt>Project name:</dt><dd><?= $projectName; ?></dd>
            <dt>Project URL:</dt><dd><?php echo $this->Html->link($projectUrl, $projectUrl, ['target' => '_blank']); ?></dd>
            <dt>Project version:</dt><dd><?= $projectVersion ?></dd>
            <dt>Current build:</dt><dd><?= $versions['current']; ?></dd>
            <dt>Deployed build:</dt><dd><?= $versions['deployed']; ?></dd>
            <dt>Previous build:</dt><dd><?= $versions['previous']; ?></dd>
        </dl>
    </div>
</div>
