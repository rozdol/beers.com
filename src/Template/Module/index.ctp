<?php
use Cake\Utility\Inflector;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;

$config = (new ModuleConfig(ConfigType::MODULE(), $this->name))->parse();
$title = isset($config->table->alias) ? $config->table->alias : Inflector::humanize(Inflector::underscore($this->name));
?>
<section class="content-header">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h4><?= $title ?></h4>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="pull-right">
                <?= $this->element('Module/Menu/index_top') ?>
            </div>
        </div>
    </div>
</section>
<section class="content">
<?php
echo $this->element('Search.Search/results', [
    'searchableFields' => $searchableFields,
    'savedSearch' => $entity,
    'searchData' => $searchData,
    'preSaveId' => $preSaveId,
    'associationLabels' => $associationLabels
]);
?>
</section>
