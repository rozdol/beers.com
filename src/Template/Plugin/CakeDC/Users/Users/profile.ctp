<section class="content-header">
    <h1>User Profile</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-3">
            <!-- Profile Image -->
            <div class="box box-primary">
                <div class="box-body box-profile">
                <?= $this->Html->image(
                    'user-image-160x160.png',
                    ['class' => 'profile-user-img img-responsive img-circle', 'alt' => 'User profile picture']
                ); ?>

                <h3 class="profile-username text-center"><?= $user['name']; ?></h3>

                <p class="text-muted text-center">System User</p>

                </div>
                <!-- /.box-body -->
            </div>
        </div>
        <div class="col-md-9">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#info" data-toggle="tab">Info</a></li>
                    <li><a href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="info">
                        <dl class="dl-horizontal">
                            <dt><?= __('ID') ?></dt>
                            <dd><?= !empty($user['id']) ? h($user['id']) : '&nbsp;' ?></dd>
                            <dt><?= __('Username') ?></dt>
                            <dd><?= !empty($user['username']) ? h($user['username']) : '&nbsp;' ?></dd>
                            <dt><?= __('Email') ?></dt>
                            <dd><?= !empty($user['email']) ? h($user['email']) : '&nbsp;' ?></dd>
                            <dt><?= __('First Name') ?></dt>
                            <dd><?= !empty($user['first_name']) ? h($user['first_name']) : '&nbsp;' ?></dd>
                            <dt><?= __('Last Name') ?></dt>
                            <dd><?= !empty($user['last_name']) ? h($user['last_name']) : '&nbsp;' ?></dd>
                        </dl>
                        <?= $this->Html->link(
                            '<i class="fa fa-lock"></i> ' . __d('Users', 'Change Password'),
                            ['plugin' => 'CakeDC/Users', 'controller' => 'Users', 'action' => 'changePassword'],
                            ['escape' => false, 'class' => 'btn btn-default btn-sm']
                        ); ?>
                    </div>
                    <!-- /.tab-pane -->

                    <div class="tab-pane" id="settings">
                        <?= $this->Form->create(null, [
                            'class' => 'form-horizontal',
                            'url' => [
                                'plugin' => 'CakeDC/Users',
                                'controller' => 'Users',
                                'action' => 'edit',
                                $user['id']
                            ]
                        ]); ?>
                            <?= $this->Form->input('Users.username', [
                                'placeholder' => __('Username'),
                                'value' => !empty($user['username']) ? h($user['username']) : null
                            ]); ?>
                            <?= $this->Form->input('Users.email', [
                                'placeholder' => __('Email'),
                                'value' => !empty($user['email']) ? h($user['email']) : null
                            ]); ?>
                            <?= $this->Form->input('Users.first_name', [
                                'placeholder' => __('First Name'),
                                'value' => !empty($user['first_name']) ? h($user['first_name']) : null
                            ]); ?>
                            <?= $this->Form->input('Users.last_name', [
                                'placeholder' => __('Last Name'),
                                'value' => !empty($user['last_name']) ? h($user['last_name']) : null
                            ]); ?>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
                                </div>
                            </div>
                        <?= $this->Form->end() ?>
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
    </div>
</section>