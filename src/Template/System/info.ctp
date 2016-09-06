<div class="row">
    <div class="col-xs-12">
        <h3><strong><?= __('System Info'); ?></strong></h3>
	</div>
</div>

<div class="row">
    <div class="col-xs-6">
        <h4>About</h4>
         <ul>
            <li><b>Project name:</b> <?= $projectName; ?></li>
            <li><b>Project URL:</b> <?php echo $this->Html->link($projectUrl, $projectUrl, ['target' => '_blank']); ?></li>
            <li><b>Project version:</b> <?= $versions['current']; ?></li>
        </ul>
    </div>
    <div class="col-xs-6">
        <?php
            // Warn the user about locally modified or incompletely
            // deployed version.
            if ($versions['current'] != $versions['deployed']) {
                print '<div class="panel panel-danger">';

                print '<div class="panel-heading">';
				print '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ';
				print '<strong>Warning</strong>';
				print '</div>';

                print '<div class="panel-body">';
                print '<p>You are running a locally modified or unsuccessfully deployed version.</p>';
                print '<ul>';
                print '<li><strong>Current version</strong>: ' . $versions['current'] . '</li>';
                print '<li><strong>Last deployed version</strong>: ' . $versions['deployed'] . '</li>';
                print '<li><strong>Previous version</strong>: ' . $versions['previous'] . '</li>';
                print '</ul>';
                print '<p>This is fine for development environments, and sometimes for test and staging ones.  But this should never happen in production!</p>';
                print '</div>';
                print '</div>';
            }
            else {
                print '&nbsp';
            }
        ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
        <?php echo $this->element('System/cakephp_plugins'); ?>
    </div>
    <div class="col-xs-6">
        <?php echo $this->element('System/server_environment'); ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
        <?php echo $this->element('System/composer_libraries'); ?>
    </div>
    <div class="col-xs-6">
        <?php echo $this->element('System/php_extensions'); ?>
    </div>
</div>
