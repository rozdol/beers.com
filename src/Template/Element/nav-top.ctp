<?php
use Cake\Core\Plugin;

if (Plugin::loaded('MessagingCenter')) {
    $unreadCount = $this->cell('MessagingCenter.Inbox::unreadCount', ['{{text}}'])->render();
    $countMessage = (int)$unreadCount;
    switch ((int)$unreadCount) {
        case 0:
            $countMessage = __d('cake', 'no new messages');
            break;

        case 1:
            $countMessage = $unreadCount . ' ' . __d('cake', 'new message');
            break;

        default:
            $countMessage = $unreadCount . ' ' . __d('cake', 'new messages');
            break;
    }
}
?>
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
            <?php if (Plugin::loaded('MessagingCenter')) : ?>
            <!-- Messages: style can be found in dropdown.less-->
            <li class="dropdown messages-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-envelope-o"></i>
                    <span class="label label-success"><?= $unreadCount; ?></span>
                </a>
                <ul class="dropdown-menu">
                    <li class="header">You have <?= $countMessage; ?> </li>
                    <li>
                        <?= $this->cell('MessagingCenter.Inbox::unreadMessages', [3, true, 35]); ?>
                    </li>
                    <li class="footer"><a href="<?= $this->Url->build(['plugin' => 'MessagingCenter', 'controller' => 'Messages', 'action' => 'folder', 'inbox']); ?>">See All Messages</a></li>
                </ul>
            </li>
            <?php endif; ?>
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