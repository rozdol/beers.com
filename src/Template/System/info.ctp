<section class="content-header">
    <h1><?= __('System Information'); ?></h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#project" data-toggle="tab" aria-expanded="true"><i class="fa fa-info-circle"></i> Project</a>
                    </li>
                    <li>
                        <a href="#cakephp" data-toggle="tab" aria-expanded="true"><i class="fa fa-birthday-cake"></i> CakePHP</a>
                    </li>
                    <li>
                        <a href="#composer" data-toggle="tab" aria-expanded="true"><i class="fa fa-book"></i> Composer</a>
                    </li>
                    <li>
                        <a href="#php" data-toggle="tab" aria-expanded="true"><i class="fa fa-heart"></i> PHP</a>
                    </li>
                    <li>
                        <a href="#server" data-toggle="tab" aria-expanded="true"><i class="fa fa-linux"></i> Server</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="project">
                        <?php echo $this->element('System/project_version'); ?>
                    </div>
                     <div class="tab-pane" id="cakephp">
                        <?php echo $this->element('System/cakephp_plugins'); ?>
                    </div>
                     <div class="tab-pane" id="composer">
                        <?php echo $this->element('System/composer_libraries'); ?>
                    </div>
                     <div class="tab-pane" id="php">
                        <?php echo $this->element('System/php_extensions'); ?>
                    </div>
                    <div class="tab-pane" id="server">
                        <?php echo $this->element('System/server_environment'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
