<?php
//
// Project information
//

// Use PROJECT_NAME environment variable or project folder name
$projectName = getenv('PROJECT_NAME') ?: basename(ROOT);

// Use PROJECT_URL environment variable or fallback URL
$projectUrl = getenv('PROJECT_URL');
$projectUrl = $projectUrl ?: \Cake\Routing\Router::fullBaseUrl();
$projectUrl = $projectUrl ?: 'https://github.com/QoboLtd/project-template-cakephp';

// Use PROJECT_VERSION environment variable or fallback
$projectVersion = getenv('PROJECT_VERSION') ?: getenv('GIT_BRANCH');
$lastCommit = shell_exec('git rev-parse --short HEAD');
$lastCommit = $lastCommit ?: 'N/A';
$projectVersion = $projectVersion ?: $lastCommit;

//
// Versions
//

// Read build/version* files or use N/A as fallback
$versions = [
	'current' => ROOT . DS . 'build' . DS . 'version',
	'deployed' => ROOT . DS . 'build' . DS . 'version.ok',
	'previous' => ROOT . DS . 'build' . DS . 'version.bak',
];
foreach ($versions as $version => $file) {
	if (is_readable($file)) {
		$versions[$version] = file_get_contents($file);
	} else {
		$versions[$version] = 'N/A';
	}
}
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
