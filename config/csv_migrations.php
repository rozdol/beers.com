<?php
use Cake\Core\Configure;

/**
 * Modify CsvMigrations configuration
 */
Configure::write('CsvMigrations.api.auth', Configure::read('API.auth'));
Configure::write('CsvMigrations.acl', [
    'class' => 'RolesCapabilities.Capabilities',
    'method' => 'checkAccess',
    'component' => 'RolesCapabilities.Capability'
]);
