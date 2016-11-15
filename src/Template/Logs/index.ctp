<?php
echo $this->Html->css('database-logs', ['block' => 'cssBottom']);

$typeLabels = [
    'emergency' => 'danger',
    'alert' => 'danger',
    'critical' => 'danger',
    'error' => 'warning',
    'warning' => 'warning',
    'notice' => 'primary',
    'info' => 'info',
    'debug' => 'info'
];

$maxMsgLength = 130;
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
                    'escape' => false,
                    'class' => 'btn btn-default'
                ]
            );
        ?>
        &nbsp;
        <?php
            echo $this->Form->postLink(
                $this->Html->icon('trash') . '&nbsp;' . __('Logs'),
                ['action' => 'reset'],
                [
                    'confirm' => 'Are you sure? This action will truncate all logs from the database.',
                    'escape' => false,
                    'class' => 'btn btn-danger'
                ]
            );
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
    $label = array_key_exists($type, $typeLabels) ? $typeLabels[$type] : 'primary';
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
    <table class="table table-hover">
        <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('created');?></th>
                <th><?php echo $this->Paginator->sort('type');?></th>
                <th><?php echo $this->Paginator->sort('message');?></th>
                <th><?php echo $this->Paginator->sort('ip', __('IP'));?></th>
                <th class="actions"><?php echo __('Actions');?></th>
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
            $title = 'Log message';
            if (preg_match('/\[(.*?Exception.*?)\]/', $message, $matches)) {
                if (!empty($matches[1])) {
                    $title = $matches[1];
                }
            }
            ?>
            <tr>
                <td><?php echo $this->Time->nice($log['created']); ?>&nbsp;</td>
                <td><?php echo h($log['type']); ?> <small>(<?php echo h($log['count']); ?>x)</small></td>
                <td class="logs-col-message">
                    <?= strlen($message) > $maxMsgLength ? h($title) : '<pre>' . h($message) . '</pre>'; ?>
                    <?php if (strlen($message) > $maxMsgLength) : ?>
                    <button class="btn btn-default" type="button" data-toggle="collapse" data-target="#collapse<?= $log['id']; ?>" aria-expanded="false" aria-controls="collapse<?= $log['id']; ?>">
                        <small><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></small>
                    </button>
                    <div class="collapse" id="collapse<?= $log['id']; ?>">
                        <pre><?php echo nl2br(h($message)); ?></pre>
                    </div>
                    <?php endif; ?>
                </td>
                <td><?php echo h($log['ip']); ?>&nbsp;</td>
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