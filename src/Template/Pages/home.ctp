<?php
$this->extend('../Layout/TwitterBootstrap/cover');

echo $this->Form->create('Example');
echo $this->Form->input('title');
echo $this->Form->input('published');