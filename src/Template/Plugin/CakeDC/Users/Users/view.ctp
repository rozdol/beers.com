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
                        <dt><?= __('Id') ?></dt>
                        <dd><?= !empty($Users->id) ? h($Users->id) : '&nbsp;' ?></dd>
                        <dt><?= __('Username') ?></dt>
                        <dd><?= !empty($Users->username) ? h($Users->username) : '&nbsp;' ?></dd>
                        <dt><?= __('Email') ?></dt>
                        <dd><?= !empty($Users->email) ? h($Users->email) : '&nbsp;' ?></dd>
                        <dt><?= __('First Name') ?></dt>
                        <dd><?= !empty($Users->first_name) ? h($Users->first_name) : '&nbsp;' ?></dd>
                        <dt><?= __('Last Name') ?></dt>
                        <dd><?= !empty($Users->last_name) ? h($Users->last_name) : '&nbsp;' ?></dd>
                        <dt><?= __('Active') ?></dt>
                        <dd><?= !empty($Users->active) ? h($Users->active) : '&nbsp;' ?></dd>
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
                        <dt><?= __('Token') ?></dt>
                        <dd><?= !empty($Users->token) ? h($Users->token) : '&nbsp;' ?></dd>
                        <dt><?= __('Api Token') ?></dt>
                        <dd><?= !empty($Users->api_token) ? h($Users->api_token) : '&nbsp;' ?></dd>
                        <dt><?= __('Token Expires') ?></dt>
                        <dd><?= !empty($Users->token_expires) ? h($Users->token_expires) : '&nbsp;' ?></dd>
                        <dt><?= __('Activation Date') ?></dt>
                        <dd><?= !empty($Users->activation_date) ? h($Users->activation_date) : '&nbsp;' ?></dd>
                        <dt><?= __('Tos Date') ?></dt>
                        <dd><?= !empty($Users->tos_date) ? h($Users->tos_date) : '&nbsp;' ?></dd>
                        <dt><?= __('Created') ?></dt>
                        <dd><?= !empty($Users->created) ? h($Users->created) : '&nbsp;' ?></dd>
                        <dt><?= __('Modified') ?></dt>
                        <dd><?= !empty($Users->modified) ? h($Users->modified) : '&nbsp;' ?></dd>
                    </dl>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    <h2 class="page-header"><i class="fa fa-link"></i> <?= __('Associated Records'); ?></h2>
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul id="relatedTabs" class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#accounts" aria-controls="accounts" role="tab" data-toggle="tab">
                            <?= __('Accounts'); ?>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="accounts">
                        <?php if (!empty($Users->social_accounts)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><?= $this->Paginator->sort(__('Id')) ?></th>
                                        <th><?= $this->Paginator->sort(__('Provider')) ?></th>
                                        <th><?= $this->Paginator->sort(__('Username')) ?></th>
                                        <th><?= $this->Paginator->sort(__('Reference')) ?></th>
                                        <th><?= $this->Paginator->sort(__('Active')) ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($Users->social_accounts as $socialAccount): ?>
                                    <tr>
                                        <td><?= h($socialAccount->id) ?></td>
                                        <td><?= h($socialAccount->provider) ?></td>
                                        <td><?= h($socialAccount->username) ?></td>
                                        <td><?= h($socialAccount->reference) ?></td>
                                        <td><?= h($socialAccount->active) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>