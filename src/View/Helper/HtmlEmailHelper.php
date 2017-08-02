<?php

namespace App\View\Helper;

use Cake\Core\Configure;
use Cake\View\Exception\MissingElementException;
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
     * @var $templateHeader
     */
    protected $templateHeader = null;

    /**
     * @var $templateFooter
     */
    protected $templateFooter = null;

    /**
     * @var $templateCss
     */
    protected $templateCss = null;

    /**
     * @var $params
     */
    protected $params = [];

    /**
     * initialize method
     *
     * @param array $config for the helper
     */
    public function initialize(array $config)
    {
        $this->templateHeader = Configure::read('EmailTemplates.header');
        $this->templateFooter = Configure::read('EmailTemplates.footer');
        $this->templateCss = Configure::read('EmailTemplates.css');

        $this->setVariables();
    }

    /**
     *  buildHtmlEmailBody method
     *
     * @param string $elementName for email
     * @param array $args optional params for element
     * @return string merged HTML and CSS
     */
    public function buildHtmlEmailBody($elementName, $args = [])
    {
        if (!$this->_View->elementExists($elementName)) {
            throw new MissingElementException("Cannot find element [$elementName]");
        }

        $args = array_merge($this->params, $args);

        $content = $this->_View->elementExists($this->templateHeader) ?
                                $this->_View->element($this->templateHeader, $args) : '';
        $content .= $this->_View->element($elementName, $args);
        $content .= $this->_View->elementExists($this->templateHeader) ?
                                $this->_View->element($this->templateFooter, $args) : '';

        $css = $this->_View->elementExists($this->templateCss) ?
                                $this->_View->element($this->templateCss) : '';

        if (!empty($content) && !empty($css)) {
            $emogrifier = new Emogrifier($content, $css);
            $content = $emogrifier->emogrify();
        }

        return $content;
    }

    /**
     * setVariables method
     *
     * @return void
     */
    protected function setVariables()
    {
        $projectName = getenv('PROJECT_NAME');
        $projectUrl = getenv('PROJECT_URL');
        $copyright = date('Y') . ' ' . $projectName . '. All rigghts reserved.';
        $logo = Configure::read('Theme.logo.mini');

        $this->params = [
            'projectName' => $projectName,
            'projectUrl' => $projectUrl,
            'copyright' => '&copy ' . date('Y') . ' ' . $projectName . '. All rigghts reserved.',
            'logo' => $logo,
            'footerMsg' => 'Qobo LTD'
        ];
    }
}
