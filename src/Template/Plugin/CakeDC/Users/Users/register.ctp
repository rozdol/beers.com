<?php
use Cake\Core\Configure;

$this->layout = 'AdminLTE/login';

$element = (string)Configure::read('Theme.templates.register');
if (! $this->elementExists($element)) {
    $element = 'register-light';
}

echo $this->element($element);
