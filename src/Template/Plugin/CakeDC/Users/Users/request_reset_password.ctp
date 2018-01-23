<?php
use Cake\Core\Configure;

$this->layout = 'AdminLTE/login';

$element = (string)Configure::read('Theme.templates.reset-password');
if (! $this->elementExists($element)) {
    $element = 'reset-password-light';
}

echo $this->element($element);
