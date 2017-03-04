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
                    <li>
                        <a href="#database" data-toggle="tab" aria-expanded="true"><i class="fa fa-database"></i> Database</a>
                    </li>
                    <li>
                        <a href="#developer" data-toggle="tab" aria-expanded="true"><i class="fa fa-wrench"></i> Developer</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="project">
                        <?php echo $this->element('System/project'); ?>
                    </div>
                     <div class="tab-pane" id="cakephp">
                        <?php echo $this->element('System/cakephp'); ?>
                    </div>
                     <div class="tab-pane" id="composer">
                        <?php echo $this->element('System/composer'); ?>
                    </div>
                     <div class="tab-pane" id="php">
                        <?php echo $this->element('System/php'); ?>
                    </div>
                    <div class="tab-pane" id="server">
                        <?php echo $this->element('System/server'); ?>
                    </div>
                    <div class="tab-pane" id="developer">
                        <?php echo $this->element('System/developer'); ?>
                    </div>
                    <div class="tab-pane" id="database">
                        <?php echo $this->element('System/database'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
