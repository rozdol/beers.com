<section class="content-header">
    <h1><?= __('System Info'); ?></h1>
</section>
<section class="content">
<div class="row">
    <div class="col-md-6">
        <?php echo $this->element('System/project_version'); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?php echo $this->element('System/cakephp_plugins'); ?>
    </div>
    <div class="col-md-6">
        <?php echo $this->element('System/server_environment'); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?php echo $this->element('System/composer_libraries'); ?>
    </div>
    <div class="col-md-6">
        <?php echo $this->element('System/php_extensions'); ?>
    </div>
</div>
</section>