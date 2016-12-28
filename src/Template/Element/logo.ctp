<?php
use Cake\Core\Configure;
?>
<a href="<?= $this->Url->build('/', ['fullBase' => true]); ?>">
    <?= Configure::read('Theme.logo.large'); ?>
</a>