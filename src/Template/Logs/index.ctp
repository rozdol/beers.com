<?php
echo $this->Html->css('database-logs', ['block' => 'css']);

$typeLabels = [
    'emergency' => 'danger',
    'alert' => 'danger',
    'critical' => 'danger',
    'error' => 'danger',
    'warning' => 'warning',
    'notice' => 'info',
    'info' => 'info',
    'debug' => 'primary'
];
?>
<section class="content-header">
    <h1>
        <?= __('Logs'); ?>
        <small><?= $this->Form->postLink(
            '<i class="fa fa-trash"></i>',
            ['plugin' => false, 'controller' => 'Logs', 'action' => 'removeDuplicates'],
            [
                'title' => __('Delete duplicates'),
                'confirm' => 'Are you sure? This action will truncate all duplicate logs from the database.',
                'escape' => false,
                'class' => 'text-danger'
            ]
        ); ?>
        <?php
        if ('localhost' === $this->request->env('SERVER_NAME')) {
            echo $this->Form->postLink(
                '<i class="fa fa-trash-o"></i>',
                ['plugin' => false, 'controller' => 'Logs', 'action' => 'reset'],
                [
                    'title' => __('Truncate all logs'),
                    'confirm' => 'Are you sure? This action will truncate all logs from the database.',
                    'escape' => false,
                    'class' => 'text-danger'
                ]
            );
        }
        ?></small>
    </h1>
</section>
<section class="content">
    <div class="box box-solid">
        <div class="box-header with-border">
            <ul class="list-inline">
                <li><?php echo $this->Html->link(
                    'ALL',
                    ['controller' => 'Logs', 'action' => 'index'],
                    ['class' => 'label label-default']
                ); ?></li>
            <?php
            // sort types by importance
            $types = array_intersect(array_keys($typeLabels), $types);
            foreach ($types as $type) {
                $label = array_key_exists($type, $typeLabels) ? $typeLabels[$type] : 'default';
                echo '<li>';
                echo $this->Html->link(
                    $type,
                    ['controller' => 'Logs', 'action' => 'index', '?' => ['type' => $type]],
                    ['class' => 'label label-' . $label]
                );
                echo '</li>';
            }
            ?>
            </ul>
            <?= $this->element('DatabaseLog.admin_filter'); ?>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-hover table-condensed table-vertical-align">
                <thead>
                    <tr>
                        <th class="col-xs-1"><?php echo $this->Paginator->sort('type');?></th>
                        <th class="col-xs-5"><?php echo $this->Paginator->sort('message');?></th>
                        <th class="col-xs-2"><?php echo $this->Paginator->sort('uri', __('URI'));?></th>
                        <th class="col-xs-1"><?php echo $this->Paginator->sort('ip', __('IP'));?></th>
                        <th class="col-xs-2"><?php echo $this->Paginator->sort('created');?></th>
                        <th class="actions col-xs-1"><?php echo __('Actions');?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($logs as $log):
                    $message = $log['message'];
                    $pos = strpos($message, 'Stack Trace:');
                    if ($pos) {
                        $message = trim(substr($message, 0, $pos));
                    }
                    $pos = strpos($message, 'Trace:');
                    if ($pos) {
                        $message = trim(substr($message, 0, $pos));
                    }
                    $title = $this->Text->truncate(
                        $message,
                        85,
                        [
                            'ellipsis' => '...',
                            'exact' => true
                        ]
                    );
                    ?>
                    <tr>
                        <td>
                        <?php $label = array_key_exists($log['type'], $typeLabels) ? $typeLabels[$log['type']] : 'default';?>
                        <span class="label label-<?= $label;?>"><?= h($log['type']); ?></span>
                        &nbsp;<small>(<?php echo h($log['count']); ?>x)</small>
                        </td>
                        <td class="logs-col-message">
                            <div class="row">
                                <div class="col-xs-9 col-lg-10 title"><?= h($title); ?></div>
                                <div class="col-xs-3 col-lg-2">
                                    <div class="text-right">
                                    <button class="btn btn-default btn-sm" type="button" data-toggle="collapse" data-target="#collapse<?= $log['id']; ?>" aria-expanded="false" aria-controls="collapse<?= $log['id']; ?>">
                                        <small><i class="fa fa-chevron-down"></i></small>
                                    </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="collapse" id="collapse<?= $log['id']; ?>">
                                            <pre><?php echo h($message); ?></pre>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo h($log['uri']); ?>&nbsp;</td>
                        <td><?php echo h($log['ip']); ?>&nbsp;</td>
                        <td><?php echo $this->Time->nice($log['created']); ?>&nbsp;</td>
                        <td class="actions">
                            <?php echo $this->Html->link(
                                '<i class="fa fa-eye"></i>',
                                [
                                    'plugin' => false,
                                    'controller' => 'Logs',
                                    'action' => 'view',
                                    $log['id'],
                                    '?' => $this->request->query
                                ],
                                [
                                    'title' => __('View'),
                                    'class' => 'btn btn-default btn-sm',
                                    'escape' => false
                                ]
                            );
                            ?>
                            <?php echo $this->Form->postLink(
                                '<i class="fa fa-trash"></i>',
                                [
                                    'plugin' => false,
                                    'controller' => 'Logs',
                                    'action' => 'delete',
                                    $log['id']
                                ],
                                [
                                    'confirm' => __('Are you sure you want to delete log # {0}?', $log['id']),
                                    'title' => __('Delete'),
                                    'class' => 'btn btn-default btn-sm',
                                    'escape' => false
                                ]
                            ); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="box-footer">
            <div class="paginator">
                <?= $this->Paginator->counter([
                    'format' => __('Showing {{start}} to {{end}} of {{count}} entries')
                ]) ?>
                <ul class="pagination pagination-sm no-margin pull-right">
                    <?= $this->Paginator->prev('&laquo;', ['escape' => false]) ?>
                    <?= $this->Paginator->numbers() ?>
                    <?= $this->Paginator->next('&raquo;', ['escape' => false]) ?>
                </ul>
            </div>
        </div>
    </div>
</section>