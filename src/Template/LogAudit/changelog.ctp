<?php
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
?>

<?php
$oldUser = null;
$oldTime = null;
$result = null;
$usersTable = null;
foreach ($changelog as $record) {
    $meta = json_decode($record->meta);
    if (empty($meta)) {
        $meta = new StdClass();
    }
    $timestamp = $record->timestamp->nice();

    if (!isset($meta->user)) {
        $meta->user = __('Unknown');
    } else {
        if (is_null($usersTable)) {
            $usersTable = TableRegistry::get('CakeDC.Users');
        }
        $meta->user = $usersTable->findById($meta->user)->first();
        $meta->user = $meta->user->username;
    }
    if ($meta->user !== $oldUser || $timestamp !== $oldTime) {
        $result .= '<table class="table table-condensed">';
        $result .= '<thead>';
            $result .= '<tr>';
                $result .= '<th colspan="3">Changed by ' . $meta->user . ' on ' . $timestamp . '</th>';
            $result .= '</tr>';
            $result .= '<tr>';
                $result .= '<th class="col-xs-2">Field</th>';
                $result .= '<th class="col-xs-5">Old Value</th>';
                $result .= '<th class="col-xs-5">New Value</th>';
            $result .= '</tr>';
        $result .= '</head>';
    }
    $oldUser = $meta->user;
    $oldTime = $timestamp;

    $result .= '<tbody>';
    $changed = json_decode($record->changed);
    $original = json_decode($record->original);
    foreach ($changed as $k => $v) {
        $old = '';
        if ($original !== null && isset($original->{$k})) {
            if ($original->{$k} !== $v) {
                $old = $original->{$k};
            }
        }
        $result .= '<tr>';
            $result .= '<td>' . Inflector::humanize($k) . '</td>';
            $result .= '<td>' . $old . '</td>';
            if (is_object($v)) {
                if (!empty($v->date) && !empty($v->timezone)) {
                    $v = new Time($v->date, $v->timezone);
                } else {
                    $v = __('Unknown value');
                }
            }
            $result .= '<td>' . $v . '</td>';
        $result .= '</tr>';
    }
    $result .= '</tbody>';
    $result .= '</table>';
}
?>

<?php
$table = TableRegistry::get($modelName);
$title = __('Changelog') . ' &raquo; ' . $table->moduleAlias() . ' &raquo; ' . $entity->{$table->displayField()};
?>

<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <div class="col-xs-6">
                <h3><strong><?= $title ?></strong></h3>
            </div>
            <div class="col-xs-6">
                <div class="h3 text-right">
                    <?php
                    list($plugin, $controller) = pluginSplit($modelName);
                    echo $this->Html->link(
                        '',
                        ['plugin' => $plugin, 'controller' => $controller, 'action' => 'view', $entity->id],
                        ['title' => __('View'), 'class' => 'btn btn-default glyphicon glyphicon-eye-open']
                    ); ?>
                </div>
            </div>
        </div>
        <div class="body well table-responsive">
            <?= $result ?>
        </div>
    </div>
</div>
