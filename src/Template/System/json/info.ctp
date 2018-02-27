<?php
$element = 'System/' . $content;
if ($this->elementExists($element)) {
    echo $this->element($element);
}
