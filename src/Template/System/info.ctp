<div class="row">
    <div class="col-xs-12">
        <h3><strong><?= __('System Info'); ?></strong></h3>
	</div>
</div>

<div class="row">
    <div class="col-xs-6">
        <?php echo $this->element('System/project_version'); ?>
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
