<?php
use Cake\ORM\TableRegistry;
use CsvMigrations\Table;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;
use RolesCapabilities\Access\AccessFactory;

$tableName = $this->name;
if ($this->plugin) {
    $tableName = $this->plugin . '.' . $tableName;
}

$table = TableRegistry::get($tableName);
// skip non CSV tables
if ($table instanceof Table) {
    $isSearchable = false;
    try {
        $config = new ModuleConfig(ConfigType::MODULE(), $this->name);
        $isSearchable = (bool)$config->parse()->table->searchable;
    } catch (Exception $e) {
        // do nothing
    }

    // skip non-searchable models
    if ($isSearchable) {
        $factory = new AccessFactory();
        $url = ['plugin' => $this->plugin, 'controller' => $this->name, 'action' => 'search'];

        if ($factory->hasAccess($url, $user)) {
            echo $this->element('search-form', ['name' => $table->moduleAlias()]);
        }
    }
}
