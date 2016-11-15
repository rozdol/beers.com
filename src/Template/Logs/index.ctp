<?php
echo $this->Html->css('database-logs', ['block' => 'cssBottom']);

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
<div class="row">
    <div class="col-xs-6">
        <h3><strong>Logs</strong></h3>
    </div>
    <div class="col-xs-6">
        <div class="h3 text-right">
        <?php
            echo $this->Form->postLink(
                $this->Html->icon('trash') . '&nbsp;' . __('Duplicates'),
                ['action' => 'removeDuplicates'],
                [
                    'confirm' => 'Are you sure? This action will truncate all duplicate logs from the database.',
                    'escape' => false,
                    'class' => 'btn btn-danger'
                ]
            );
        ?>
        &nbsp;
        <?php
        if ('localhost' === $this->request->env('SERVER_NAME')) {
            echo $this->Form->postLink(
                $this->Html->icon('trash') . '&nbsp;' . __('Logs'),
                ['action' => 'reset'],
                [
                    'confirm' => 'Are you sure? This action will truncate all logs from the database.',
                    'escape' => false,
                    'class' => 'btn btn-danger'
                ]
            );
        }
        ?>
        </div>
    </div>
</div>
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

<?php echo $this->element('DatabaseLog.admin_filter'); ?>

<div class="table-responsive">
    <table class="table table-hover table-condensed">
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
                        <div class="col-xs-9 col-lg-10 title">
                            <?= h($title); ?>
                        </div>
                        <div class="col-xs-3 col-lg-2">
                            <div class="text-right">
                            <button class="btn btn-default" type="button" data-toggle="collapse" data-target="#collapse<?= $log['id']; ?>" aria-expanded="false" aria-controls="collapse<?= $log['id']; ?>">
                                <small><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></small>
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
                        null,
                        ['action' => 'view', $log['id'], '?' => $this->request->query],
                        ['title' => __('View'), 'class' => 'btn btn-default glyphicon glyphicon-eye-open']
                    );
                    ?>
                    <?php echo $this->Form->postLink(
                        null,
                        ['action' => 'delete', $log['id']],
                        [
                            'confirm' => __('Are you sure you want to delete this log # {0}?', $log['id']),
                            'title' => __('Delete'),
                            'class' => 'btn btn-default glyphicon glyphicon-trash'
                        ]
                    ); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="paginator">
    <ul class="pagination">
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers(['before' => '', 'after' => '']) ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
    </ul>
    <p>
        <?= $this->Paginator->counter(
            ['format' => __('Showing {{start}} to {{end}} of {{count}} entries')
        ]) ?>
    </p>
</div>