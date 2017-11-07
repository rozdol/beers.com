<?php
use CsvMigrations\FieldHandlers\FieldHandlerFactory;

$factory = new FieldHandlerFactory($this);

$field = trim($column, '_');

echo $factory->renderValue($config['modelName'], $field, $item[$column]);
