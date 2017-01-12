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
$projectVersion = $projectVersion ?: 'N/A';

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
<div class="box box-default">
    <div class="box-header with-border">
        <i class="fa fa-info-circle"></i>
        <h3 class="box-title"><?= __('About') ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <ul>
            <li><b>Project name:</b> <?= $projectName; ?></li>
            <li><b>Project URL:</b> <?php echo $this->Html->link($projectUrl, $projectUrl, ['target' => '_blank']); ?></li>
            <li><b>Project version:</b> <?= $projectVersion ?></li>
            <li><b>Current build:</b> <?= $versions['current']; ?></li>
            <li><b>Deployed build:</b> <?= $versions['deployed']; ?></li>
            <li><b>Previous build:</b> <?= $versions['previous']; ?></li>
        </ul>
    </div>
</div>
<?php
// Warn the user about locally modified or incompletely deployed version.  Skip for CLI and localhost.
$warnAboutDeploy = ($versions['current'] != $versions['deployed']) ? true : false;
// Skip warning for CLI and localhost
$warnAboutDeploy = (empty($_SERVER['SERVER_NAME']) || (strtolower($_SERVER['SERVER_NAME']) == 'localhost')) ? false : $warnAboutDeploy;

if ($warnAboutDeploy) : ?>
    <div class="box box-danger">
        <div class="box-header with-border">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <strong>Warning</strong>
        </div>
        <div class="box-body">
            <p>You are running a locally modified or unsuccessfully deployed build.</p>
            <p>This is fine for development environments, and sometimes for test and staging ones. But this should never happen in production!</p>
        </div>
    </div>
<?php endif; ?>