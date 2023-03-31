<?php
namespace Chamilo\Libraries\Format\Form\Element;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\File\Path;

/**
 *
 * @package Chamilo\Libraries\Format\Form\Element
 */
class HTML_QuickForm_option_orderer extends \HTML_QuickForm_hidden
{

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
     *
     * @see HTML_QuickForm_input::toHtml()
     */
    public function toHtml(): string
    {
        $html = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'OptionOrderer.js');
        $html .= $this->getFrozenHtml();
        return $html;
    }

    /**
     *
     * @see HTML_QuickForm_element::getFrozenHtml()
     */
    public function getFrozenHtml(): string
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

    /**
     *
     * @see HTML_QuickForm_input::getValue()
     */
    public function getValue()
    {
        return explode($this->separator, parent::getValue());
    }

    /**
     *
     * @see HTML_QuickForm_input::exportValue()
     */
    public function exportValue(array &$submitValues, bool $assoc = false)
    {
        return $this->getValue();
    }
}
