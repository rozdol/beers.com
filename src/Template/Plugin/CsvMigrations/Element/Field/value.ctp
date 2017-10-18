<?php
use Cake\Utility\Inflector;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;
use RolesCapabilities\Access\AccessFactory;

$tableName = $field['model'];
if ($field['plugin']) {
    $tableName = $field['plugin'] . '.' . $tableName;
}

$renderOptions = ['entity' => $options['entity'], 'imageSize' => 'small'];

$isTranslatable = function ($tableName, $fieldName) {
    // Read translatable from config.ini
    $mc = new ModuleConfig(ConfigType::MODULE(), Inflector::camelize($tableName));
    $config = $mc->parse();

    if (!(bool)$config->table->translatable) {
        return false;
    }

    // Read field options from fields.ini
    $mc = new ModuleConfig(ConfigType::FIELDS(), Inflector::camelize($tableName));
    $config = $mc->parse();

    if (!isset($config->{$fieldName}->translatable)) {
        return false;
    }

    return (bool)$config->{$fieldName}->translatable;
};

$fieldValue = $options['entity']->get($field['name']);

$label = $factory->renderName($tableName, $field['name'], $renderOptions);
$value = $factory->renderValue($tableName, $field['name'], $options['entity'], $renderOptions);
?>
<div class="col-xs-4 col-md-2 text-right"><strong><?= $label ?>:</strong></div>
<div class="col-xs-8 col-md-4">
    <?php if ($fieldValue) : ?>
        <?php if ($isTranslatable($tableName, $field['name'])) : ?>
            <?php
            $accessFactory = new AccessFactory();
            $url = ['plugin' => 'Translations', 'controller' => 'Translations', 'action' => 'addOrUpdate'];
            ?>
            <?php if ($accessFactory->hasAccess($url, $user)) : ?>
                <a
                    href="#translations_translate_id_modal"
                    data-toggle="modal"
                    data-record="<?= $options['entity']->get('id') ?>"
                    data-model="<?= $tableName ?>"
                    data-field="<?= $field['name'] ?>"
                    data-value="<?= $fieldValue ?>">
                        <i class="fa fa-globe"></i>
                </a>&nbsp;
                <?= $this->element('Translations.modal_add') ?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
    <?= !empty($value) ? $value : '&nbsp;' ?>
</div>