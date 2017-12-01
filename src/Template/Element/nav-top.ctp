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
                    <?php
                    if (!empty($user['image'])) {
                        echo '<img src="' . $user['image'] . '" class="user-image" />';
                    } else {
                        echo $this->Html->image('user-image-160x160.png', ['class' => 'user-image']);
                    }
                    ?>
                    <span class="hidden-xs"><?= $user['name']; ?></span>
                </a>
                <ul class="dropdown-menu">
                    <!-- User image -->
                    <li class="user-header">
                        <?php
                        if (!empty($user['image'])) {
                            echo '<img src="' . $user['image'] . '" class="img-circle" />';
                        } else {
                            echo $this->Html->image('user-image-160x160.png', ['class' => 'img-circle']);
                        }
                        ?>

                        <p>
                            <?= $user['name']; ?>
                            <small><?= __d('cake', 'Member since') ?> <?= $user['created']->i18nFormat('LLLL yyyy') ?></small>
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
