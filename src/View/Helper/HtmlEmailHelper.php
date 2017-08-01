<?php

namespace App\View\Helper;

use Cake\View\Helper;
use Pelago\Emogrifier;

class HtmlEmailHelper extends Helper
{
    public function buildHtmlEmailBody($htmlBody, $css)
    {
        $emogrifier = new Emogrifier($htmlBody, $css);
        
        return $emogrifier->emogrify();
    }
}
