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

$typeIcons = [
    'emergency' => 'fa fa-exclamation-triangle bg-red',
    'alert' => 'fa fa-exclamation-triangle bg-red',
    'critical' => 'fa fa-exclamation-triangle bg-red',
    'error' => 'fa fa-exclamation-triangle bg-red',
    'warning' => 'fa fa-exclamation-triangle bg-yellow',
    'notice' => 'fa fa-info-circle bg-blue',
    'info' => 'fa fa-info-circle bg-blue',
    'debug' => 'fa-wrench bg-green'
];

?>
<section class="content-header">
    <h1><?= __('Logs'); ?>
        <div class="pull-right">
            <div class="btn-group btn-group-sm" role="group">
            <?php echo $this->Form->postLink(
                '<i class="fa fa-trash"></i> ' . __('Delete duplicates'),
                ['plugin' => false, 'controller' => 'Logs', 'action' => 'removeDuplicates'],
                [
                    'title' => __('Delete duplicates'),
                    'confirm' => 'Are you sure? This action will truncate all duplicate logs from the database.',
                    'escape' => false,
                    'class' => 'btn btn-danger'
                ]
            );

            if ('localhost' === $this->request->env('SERVER_NAME')) {
                echo $this->Form->postLink(
                    '<i class="fa fa-trash-o"></i> ' . __('Truncate all logs'),
                    ['plugin' => false, 'controller' => 'Logs', 'action' => 'reset'],
                    [
                        'title' => __('Truncate all logs'),
                        'confirm' => 'Are you sure? This action will truncate all logs from the database.',
                        'escape' => false,
                        'class' => 'btn btn-danger'
                    ]
                );
            }
            ?>
            </div>
        </div>
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
                
        <!-- Timeline start -->
        <?php $displayed_date = ''; ?>
        <ul class="timeline">
        <?php foreach ($logs as $log) : ?>
        <?php 
            $date = $log['created']->i18nFormat('yyyy-MM-dd');
            if ($displayed_date != $date) {
                $displayed_date = $date;
                ?>
                <!-- timeline time label -->
                <li class="time-label">
                    <span class="bg-red">
                        <?= $displayed_date ?>
                    </span>
                </li>
                <!-- /.timeline-label -->
                <?php
            }
        ?>
       
        <!-- timeline item -->
        <li>
            <!-- timeline icon -->
            <i class="<?= $typeIcons[$log['type']] ?>"></i>
            <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i> <?= $log['created']->i18nFormat('H:m:s') ?></span>

                <h2 class="timeline-header"><?= ucfirst($log['type']); ?></h2>

                <div class="timeline-body">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-4 col-md-2 text-right">
                                <strong><?= __('Hostname'); ?></strong>
                            </div>
                            <div class="col-xs-8 col-md-4">
                                <?= h($log['hostname']); ?>
                            </div>
                            <div class="col-xs-4 col-md-2 text-right">
                                <strong><?= __('IP'); ?></strong>
                            </div>
                            <div class="col-xs-8 col-md-4">
                                <?= h($log['ip']); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4 col-md-2 text-right">
                                <strong><?= __('Uri'); ?></strong>
                            </div>
                            <div class="col-xs-8 col-md-4">
                                <?= h($log['uri']); ?>
                            </div>
                            <div class="col-xs-4 col-md-2 text-right">
                                <strong><?= __('Referrer'); ?></strong>
                            </div>
                            <div class="col-xs-8 col-md-4">
                                <?= h($log['refer']); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 text-right">
                                <strong><?= __('Message'); ?></strong>
                            </div>
                            <div class="col-md-10">
                                <pre><small><?= trim(h($log['message'])); ?></small></pre>
                            </div>
                        </div>
                        <?php if (!empty($log['context']['scope'])) : ?>
                        <div class="row">
                            <div class="col-md-2 text-right">
                                <strong><?= __('Context'); ?></strong>
                            </div>
                            <div class="col-md-10">
                                <pre><small><?= h($log['context']); ?></small></pre>
                            </div>                            
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
        <!-- END timeline item -->

        </ul>
        <!-- Timeline end -->
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
