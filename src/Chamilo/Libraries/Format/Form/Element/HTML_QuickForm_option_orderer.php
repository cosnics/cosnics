<?php
/**
 *
 * @package common.html.formvalidator.Element
 */
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\File\Path;
class HTML_QuickForm_option_orderer extends HTML_QuickForm_hidden
{

    private $options;

    public function __construct($name = null, $label = null, $options = null, $separator = '|', $attributes = array())
    {
        $this->separator = $separator;
        $value = (isset($_REQUEST[$name]) ? $_REQUEST[$name] : implode($this->separator, array_keys($options)));
        parent::__construct($name, $value, $attributes);
        $this->options = $options;
    }

    public function toHtml()
    {
        $html = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'OptionOrderer.js');
        $html .= $this->getFrozenHtml();
        return $html;
    }

    public function getFrozenHtml()
    {
        $html = '<ol class="option-orderer oord-name_' . $this->getName() . '">';
        $order = $this->getValue();
        foreach ($order as $index)
        {
            $html .= '<li class="oord-value_' . $index . '">' . $this->options[$index] . '</li>';
        }
        $html .= '</ol>';
        $html .= parent::toHtml();
        return $html;
    }

    public function getValue()
    {
        return explode($this->separator, parent::getValue());
    }

    public function exportValue()
    {
        return $this->getValue();
    }
}
