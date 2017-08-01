<?php
/**
 * Copyright 2010 - 2015, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2015, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

use \Pelago\Emogrifier;

$emogrifier = new Emogrifier();

$activationUrl = [
    '_full' => true,
    'plugin' => 'CakeDC/Users',
    'controller' => 'Users',
    'action' => 'validateEmail',
    isset($token) ? $token : ''
];

$content = $this->element('Email/header');
$content .= $this->element('Email/activation_link', ['activationUrl' => $activationUrl]);
$content .= $this->element('Email/footer');

$css = $this->element('Email/css');

$emogrifier->setHtml($content);
$emogrifier->setCss($css);

$mergedContent = $emogrifier->emogrifyBodyContent();

echo $mergedContent;
?>
