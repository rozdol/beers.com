<?php use Cake\Utility\Inflector; ?>
<section class="content-header">
    <h1><?= $this->Html->link(__('Users'), ['action' => 'index']) . ' &raquo; ' . h($Users->username) ?></h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <i class="fa fa-user"></i>

                    <h3 class="box-title">Details</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt><?= __('ID') ?></dt>
                        <dd><?= $Users->has('id') ? h($Users->id) : '&nbsp;' ?></dd>
                        <dt><?= __('Username') ?></dt>
                        <dd><?= $Users->has('username') ? h($Users->username) : '&nbsp;' ?></dd>
                        <dt><?= __('Email') ?></dt>
                        <dd><?= $Users->has('email') ? h($Users->email) : '&nbsp;' ?></dd>
                        <dt><?= __('First Name') ?></dt>
                        <dd><?= $Users->has('first_name') ? h($Users->first_name) : '&nbsp;' ?></dd>
                        <dt><?= __('Last Name') ?></dt>
                        <dd><?= $Users->has('last_name') ? h($Users->last_name) : '&nbsp;' ?></dd>
                        <dt><?= __('Active') ?></dt>
                        <dd><?= $Users->has('active') && $Users->active ? __('Yes') : __('No') ?></dd>
                        <dt><?= __('Gender') ?></dt>
                        <dd><?= $Users->has('gender') ? Inflector::humanize(h($Users->gender)) : '&nbsp;' ?></dd>
                        <dt><?= __('Birthdate') ?></dt>
                        <dd><?= $Users->has('birthdate') ? $Users->birthdate->i18nFormat('yyyy-MM-dd') : '&nbsp;' ?></dd>
                    </dl>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-6">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <i class="fa fa-info-circle"></i>

                    <h3 class="box-title">Other</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt><?= __('Phone Office') ?></dt>
                        <dd><?= $Users->has('phone_office') ? h($Users->phone_office) : '&nbsp;' ?></dd>
                        <dt><?= __('Phone Home') ?></dt>
                        <dd><?= $Users->has('phone_home') ? h($Users->phone_home) : '&nbsp;' ?></dd>
                        <dt><?= __('Phone Mobile') ?></dt>
                        <dd><?= $Users->has('phone_mobile') ? h($Users->phone_mobile) : '&nbsp;' ?></dd>
                        <dt><?= __('Created') ?></dt>
                        <dd><?= $Users->has('created') ? $Users->created->i18nFormat('yyyy-MM-dd HH:mm') : '&nbsp;' ?></dd>
                        <dt><?= __('Modified') ?></dt>
                        <dd><?= $Users->has('modified') ? $Users->modified->i18nFormat('yyyy-MM-dd HH:mm') : '&nbsp;' ?></dd>
                    </dl>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
</section>
