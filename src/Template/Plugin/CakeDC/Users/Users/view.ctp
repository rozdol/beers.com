<div class="row">
    <div class="col-xs-12">
        <h3><strong><?= $this->Html->link(__('Users'), ['action' => 'index']) . ' &raquo; ' . h($Users->username) ?></strong></h3>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">&nbsp;</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('Id') ?>:</strong>
                    </div>
                    <div class="col-xs-8 col-md-4"><?= !empty($Users->id) ? h($Users->id) : '&nbsp;' ?></div>
                    <div class="clearfix visible-xs visible-sm"></div>
                    <div class="col-xs-4 col-md-2 text-right">&nbsp;</div>
                    <div class="col-xs-8 col-md-4">&nbsp;</div>
                </div>
                <div class="row">
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('Username') ?>:</strong>
                    </div>
                    <div class="col-xs-8 col-md-4"><?= !empty($Users->username) ? h($Users->username) : '&nbsp;' ?></div>
                    <div class="clearfix visible-xs visible-sm"></div>
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('Email') ?>:</strong>
                    </div>
                    <div class="col-xs-8 col-md-4"><?= !empty($Users->email) ? h($Users->email) : '&nbsp;' ?></div>
                </div>
                <div class="row">
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('First Name') ?>:</strong>
                    </div>
                    <div class="col-xs-8 col-md-4"><?= !empty($Users->first_name) ? h($Users->first_name) : '&nbsp;' ?></div>
                    <div class="clearfix visible-xs visible-sm"></div>
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('Last Name') ?>:</strong>
                    </div>
                    <div class="col-xs-8 col-md-4"><?= !empty($Users->last_name) ? h($Users->last_name) : '&nbsp;' ?></div>
                </div>
                <div class="row">
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('Token') ?>:</strong>
                    </div>
                    <div class="col-xs-8 col-md-4"><?= !empty($Users->token) ? h($Users->token) : '&nbsp;' ?></div>
                    <div class="clearfix visible-xs visible-sm"></div>
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('Api Token') ?>:</strong>
                    </div>
                    <div class="col-xs-8 col-md-4"><?= !empty($Users->api_token) ? h($Users->api_token) : '&nbsp;' ?></div>
                </div>
                <div class="row">
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('Active') ?>:</strong>
                    </div>
                    <div class="col-xs-8 col-md-4"><?= !empty($Users->active) ? h($Users->active) : '&nbsp;' ?></div>
                    <div class="clearfix visible-xs visible-sm"></div>
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('Token Expires') ?>:</strong>
                    </div>
                    <div class="col-xs-8 col-md-4"><?= !empty($Users->token_expires) ? h($Users->token_expires) : '&nbsp;' ?></div>
                </div>
                <div class="row">
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('Activation Date') ?>:</strong>
                    </div>
                    <div class="col-xs-8 col-md-4"><?= !empty($Users->activation_date) ? h($Users->activation_date) : '&nbsp;' ?></div>
                    <div class="clearfix visible-xs visible-sm"></div>
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('Tos Date') ?>:</strong>
                    </div>
                    <div class="col-xs-8 col-md-4"><?= !empty($Users->tos_date) ? h($Users->tos_date) : '&nbsp;' ?></div>
                </div>
                <div class="row">
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('Created') ?>:</strong>
                    </div>
                    <div class="col-xs-8 col-md-4"><?= !empty($Users->created) ? h($Users->created) : '&nbsp;' ?></div>
                    <div class="clearfix visible-xs visible-sm"></div>
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('Modified') ?>:</strong>
                    </div>
                    <div class="col-xs-8 col-md-4"><?= !empty($Users->modified) ? h($Users->modified) : '&nbsp;' ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <h3><?= __('Associated Records'); ?></h3>
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
