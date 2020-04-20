<?php

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

/**
 *
 * @package Chamilo\Libraries\Format\Form\Element
 */
class HTML_QuickForm_option_orderer extends HTML_QuickForm_hidden
{

    /**
     * @var string
     */
    private $separator;

    /**
     *
     * @var string[]
     */
    private $options;

    /**
     *
     * @param string $name
     * @param string $label
     * @param string[] $options
     * @param string $separator
     * @param string[] $attributes
     */
    public function __construct($name = null, $label = null, $options = null, $separator = '|', $attributes = array())
    {
        $this->separator = $separator;
        $value = (isset($_REQUEST[$name]) ? $_REQUEST[$name] : implode($this->separator, array_keys($options)));
        parent::__construct($name, $value, $attributes);
        $this->options = $options;
    }

    /**
     * Returns a 'safe' element's value
     *
     * @param array   array of submitted values to search
     * @param bool    whether to return the value as associative array
     *
     * @access public
     * @return mixed
     */
    function exportValue(&$submitValues, $assoc = false)
    {
        return $this->getValue();
    }

    /**
     * Returns the value of field without HTML tags
     *
     * @return    string
     * @since     1.0
     * @access    public
     */
    public function getFrozenHtml()
    {
        $html = array();

        $html[] = '<ol class="option-orderer oord-name_' . $this->getName() . '">';

        foreach ($this->getValue() as $index)
        {
            $html[] = '<li class="oord-value_' . $index . '">' . $this->options[$index] . '</li>';
        }

        $html[] = '</ol>';
        $html[] = parent::toHtml();

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the value of the form element
     *
     * @return    string
     * @since     1.0
     * @access    public
     */
    public function getValue()
    {
        return explode($this->separator, parent::getValue());
    }

    /**
     * Returns the input field in HTML
     *
     * @return    string
     * @since     1.0
     * @access    public
     */
    public function toHtml()
    {
        $html = array();

        $html[] = ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'OptionOrderer.js'
        );
        $html[] = $this->getFrozenHtml();

        return implode(PHP_EOL, $html);
    }
}
