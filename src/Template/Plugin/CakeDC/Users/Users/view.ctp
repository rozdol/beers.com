<div class="row">
    <div class="col-xs-12">
        <h3><strong><?= $this->Html->link(__('Users'), ['action' => 'index']) . ' &raquo; ' . h($Users->username) ?></strong></h3>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">&nbsp;</h3>
            </div>
            <table class="table table-hover">
                <tr>
                    <td class="col-xs-3 text-right">
                        <strong><?= __('Id') ?>:</strong>
                    </td>
                    <td class="col-xs-3"><?= h($Users->id) ?></td>
                    <td class="col-xs-3 text-right">&nbsp;</td>
                    <td class="col-xs-3">&nbsp;</td>
                </tr>
                <tr>
                    <td class="col-xs-3 text-right">
                        <strong><?= __('Username') ?>:</strong>
                    </td>
                    <td class="col-xs-3"><?= h($Users->username) ?></td>
                    <td class="col-xs-3 text-right">
                        <strong><?= __('Email') ?>:</strong>
                    </td>
                    <td class="col-xs-3"><?= h($Users->email) ?></td>
                </tr>
                <tr>
                    <td class="col-xs-3 text-right">
                        <strong><?= __('First Name') ?>:</strong>
                    </td>
                    <td class="col-xs-3"><?= h($Users->first_name) ?></td>
                    <td class="col-xs-3 text-right">
                        <strong><?= __('Last Name') ?>:</strong>
                    </td>
                    <td class="col-xs-3"><?= h($Users->last_name) ?></td>
                </tr>
                <tr>
                    <td class="col-xs-3 text-right">
                        <strong><?= __('Token') ?>:</strong>
                    </td>
                    <td class="col-xs-3"><?= h($Users->token) ?></td>
                    <td class="col-xs-3 text-right">
                        <strong><?= __('Api Token') ?>:</strong>
                    </td>
                    <td class="col-xs-3"><?= h($Users->api_token) ?></td>
                </tr>
                <tr>
                    <td class="col-xs-3 text-right">
                        <strong><?= __('Active') ?>:</strong>
                    </td>
                    <td class="col-xs-3"><?= h($Users->active) ?></td>
                    <td class="col-xs-3 text-right">
                        <strong><?= __('Token Expires') ?>:</strong>
                    </td>
                    <td class="col-xs-3"><?= h($Users->token_expires) ?></td>
                </tr>
                <tr>
                    <td class="col-xs-3 text-right">
                        <strong><?= __('Activation Date') ?>:</strong>
                    </td>
                    <td class="col-xs-3"><?= h($Users->activation_date) ?></td>
                    <td class="col-xs-3 text-right">
                        <strong><?= __('Tos Date') ?>:</strong>
                    </td>
                    <td class="col-xs-3"><?= h($Users->tos_date) ?></td>
                </tr>
                <tr>
                    <td class="col-xs-3 text-right">
                        <strong><?= __('Created') ?>:</strong>
                    </td>
                    <td class="col-xs-3"><?= h($Users->created) ?></td>
                    <td class="col-xs-3 text-right">
                        <strong><?= __('Modified') ?>:</strong>
                    </td>
                    <td class="col-xs-3"><?= h($Users->modified) ?></td>
                </tr>
            </table>
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
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
