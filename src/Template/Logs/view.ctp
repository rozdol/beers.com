<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */ 
?>
<div class="row">
    <div class="col-xs-6">
        <h3>
            <strong>
                <?= $this->Html->link(
                    __('Logs'),
                    ['action' => 'index']
                ) . ' &raquo; ' . h($log['type']); ?>
            </strong>
        </h3>
    </div>
    <div class="col-xs-6">
        <div class="h3 text-right">
            <?= $this->Form->postLink(
                $this->Html->icon('trash'),
                ['action' => 'delete', $log['id']],
                [
                    'confirm' => __('Are you sure?'),
                    'escape' => false,
                    'class' => 'btn btn-default'
                ]
            ); ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">&nbsp;</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('type'); ?></strong>
                    </div>
                    <div class="col-xs-8 col-md-4">
                        <?= h($log['type']); ?>
                    </div>
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('Uri'); ?></strong>
                    </div>
                    <div class="col-xs-8 col-md-4">
                        <?= h($log['uri']); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('Referrer'); ?></strong>
                    </div>
                    <div class="col-xs-8 col-md-4">
                        <?= h($log['refer']); ?>
                    </div>
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('Hostname'); ?></strong>
                    </div>
                    <div class="col-xs-8 col-md-4">
                        <?= h($log['hostname']); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('IP'); ?></strong>
                    </div>
                    <div class="col-xs-8 col-md-4">
                        <?= h($log['ip']); ?>
                    </div>
                    <div class="col-xs-4 col-md-2 text-right">
                        <strong><?= __('Created'); ?></strong>
                    </div>
                    <div class="col-xs-8 col-md-4">
                        <?= $this->Time->nice($log['created']); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 text-right">
                        <strong><?= __('Context'); ?></strong>
                    </div>
                    <div class="col-md-10">
                        <pre><?= h($log['context']); ?></pre>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 text-right">
                        <strong><?= __('Message'); ?></strong>
                    </div>
                    <div class="col-md-10">
                        <pre><?= trim(h($log['message'])); ?></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
