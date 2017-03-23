<nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </a>

    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            <!-- User Account: style can be found in dropdown.less -->
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <?php echo $this->Html->image('user-image-160x160.png', array('class' => 'user-image', 'alt' => 'User Image')); ?>
                    <span class="hidden-xs"><?= $user['name']; ?></span>
                </a>
                <ul class="dropdown-menu">
                    <!-- User image -->
                    <li class="user-header">
                        <?php echo $this->Html->image('user-image-160x160.png', array('class' => 'img-circle', 'alt' => 'User Image')); ?>

                        <p>
                            <?= $user['name']; ?>
                            <small><?= __d('cake', 'Member since') ?> <?= $this->Month->shortName($user['created']->i18nFormat('M')) ?> <?= $user['created']->i18nFormat('yyyy'); ?></small>
                        </p>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <div class="pull-left">
                            <?= $this->Html->link(
                                __d('cake', 'Profile'),
                                '/users/profile',
                                ['class' => 'btn btn-default btn-flat']
                            ); ?>
                        </div>
                        <div class="pull-right">
                            <?= $this->Html->link(
                                __d('cake', 'Sign out'),
                                '/users/logout',
                                ['class' => 'btn btn-default btn-flat']
                            ); ?>
                        </div>
                    </li>
                </ul>
            </li>
            <!-- Control Sidebar Toggle Button -->
            <li>
                <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
            </li>
        </ul>
    </div>
</nav>