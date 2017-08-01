<?php

namespace App\View\Helper;

use Cake\View\Helper;
use Pelago\Emogrifier;

/**
 * HtmlEmailHelper
 *
 *  includes functionlity to merge HTML and CSS in the email
 */
class HtmlEmailHelper extends Helper
{
    /**
     *  buildHtmlEmailBody method
     *
     * @param string $htmlBody for email
     * @param string $css for email
     * @return string merged HTML and CSS
     */
    public function buildHtmlEmailBody($htmlBody, $css)
    {
        $emogrifier = new Emogrifier($htmlBody, $css);

        return $emogrifier->emogrify();
    }
}
