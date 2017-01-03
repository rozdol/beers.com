<?php
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\Utility\Inflector;

$oldUser = null;
$oldDate = null;
$bgColors = [
    'red',
    'yellow',
    'aqua',
    'blue',
    'light-blue',
    'green',
    'navy',
    'teal',
    'olive',
    'lime',
    'orange',
    'fuchsia',
    'purple',
    'maroon',
    'black'
];
$countHalf = count($bgColors) / 2;
?>
<section class="content-header">
    <h1>
        <?= __('Changelog')?> &raquo; <?= $this->Html->link(
            $entity->{$displayField},
            ['plugin' => $this->plugin, 'controller' => $this->name, 'action' => 'view', $entity->id],
            ['escape' => false]
        ); ?>
    </h1>
</section>
<section class="content">
<div class="row">
    <div class="col-xs-12">
        <ul class="timeline">
<?php foreach ($changelog as $record) : ?>
    <?php
    $meta = json_decode($record->meta);
    if (empty($meta)) {
        $meta = new StdClass();
    }
    $date = $record->timestamp->i18nFormat('d MMM. YYY');

    if (!isset($meta->user)) {
        $meta->user = __('Unknown');
    } else {
        $user = $usersTable->findById($meta->user)->first();
        $meta->user = empty($user) ? $meta->user : $user->name;
    }
    ?>
    <?php if ($meta->user !== $oldUser || $date !== $oldDate) : ?>
        <li class="time-label"><span class="bg-<?= current($bgColors) ?>"><?= $date ?></span></li>
    <?php endif; ?>
        <li>
            <i class="fa fa-book bg-<?= $bgColors[key($bgColors) + $countHalf] ?>"></i>
            <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i>
                    <?= $record->timestamp->timeAgoInWords([
                        'format' => 'MMM d, YYY | HH:mm:ss',
                        'end' => '1 month'
                    ]) ?>
                </span>
                <h3 class="timeline-header">
                    <a href="#"><?= $meta->user; ?></a> made the following changes:
                </h3>
                <div class="timeline-body">
                    <table class="table table-hover table-condensed table-vertical-align">
                        <thead>
                            <tr>
                                <th class="col-xs-2">Field</th>
                                <th class="col-xs-5">Old Value</th>
                                <th class="col-xs-5">New Value</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php
    $changed = json_decode($record->changed);
    $original = json_decode($record->original);
    foreach ($changed as $k => $v) :
        $old = '';
        if ($original !== null && isset($original->{$k})) {
            if ($original->{$k} !== $v) {
                $old = $original->{$k};
            }
        }
    ?>
    <tr>
        <td><?= Inflector::humanize($k) ?></td>
        <td><?= $old ?></td>
        <?php
        if (is_object($v)) {
            if (!empty($v->date) && !empty($v->timezone)) {
                $v = new Time($v->date, $v->timezone);
            } else {
                $v = __('Unknown value');
            }
        }
        ?>
        <td><?= $v ?></td>
    </tr>
    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </li>
<?php
$oldUser = $meta->user;
$oldDate = $date;
next($bgColors);
?>
<?php endforeach; ?>
        </ul>
        <div class="body well table-responsive">
            <?= $result ?>
        </div>
    </div>
</div>