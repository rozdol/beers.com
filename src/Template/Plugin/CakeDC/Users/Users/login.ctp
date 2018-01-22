<?php
use Cake\Core\Configure;

$this->layout = 'AdminLTE/login';

$element = (string)Configure::read('Theme.templates.login');
if (! $this->elementExists($element)) {
    $element = 'login-default';
}

echo $this->element($element);
