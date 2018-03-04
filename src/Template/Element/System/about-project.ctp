<?php
//
// About project section
//

use App\SystemInfo\Project;

$projectName = Project::getName();
$projectVersion = Project::getDisplayVersion();
$projectLogo = Project::getLogo('large');

?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">About project</h3>
    </div>
    <div class="box-body">
        <p><?= $projectLogo ?></p>
        <p>Welcome to <b><?= $projectName ?></b>.  You are using version <b><?= $projectVersion ?></b>.
    </div>
</div>
