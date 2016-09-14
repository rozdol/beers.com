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
<h4>About</h4>
<ul>
    <li><b>Project name:</b> <?= $projectName; ?></li>
    <li><b>Project URL:</b> <?php echo $this->Html->link($projectUrl, $projectUrl, ['target' => '_blank']); ?></li>
    <li><b>Project version:</b> <?= $projectVersion ?></li>
    <li><b>Current build:</b> <?= $versions['current']; ?></li>
    <li><b>Deployed build:</b> <?= $versions['deployed']; ?></li>
    <li><b>Previous build:</b> <?= $versions['previous']; ?></li>
</ul>
<?php
// Warn the user about locally modified or incompletely deployed version.  Skip for CLI and localhost.
$warnAboutDeploy = ($versions['current'] != $versions['deployed']) ? true : false;
// Skip warning for CLI and localhost
$warnAboutDeploy = (empty($_SERVER['SERVER_NAME']) || (strtolower($_SERVER['SERVER_NAME']) == 'localhost')) ? false : $warnAboutDeploy;

if ($warnAboutDeploy) : ?>
    <div class="panel panel-danger">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <strong>Warning</strong>
        </div>
        <div class="panel-body">
            <p>You are running a locally modified or unsuccessfully deployed build.</p>
            <p>This is fine for development environments, and sometimes for test and staging ones.  But this should never happen in production!</p>
        </div>
    </div>
<?php endif; ?>
