<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b>:
		<?php echo getenv('PROJECT_VERSION') ?: getenv('GIT_BRANCH'); ?>
    </div>
    <strong>
		Copyright &copy; <?php echo date('Y'); ?>,
		<?php echo getenv('PROJECT_NAME') ?: basename(ROOT); ?>.
	</strong>
	All rights reserved.
</footer>
<?php
// @todo find a way to load this as part of 'block' => 'css'
echo $this->Html->css('custom');
$this->Html->script('general.js', ['block' => 'scriptBotton']);

