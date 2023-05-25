<?php
namespace Chamilo\Libraries\Format\Form\Element;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use HTML_QuickForm_hidden;

/**
 * @package Chamilo\Libraries\Format\Form\Element
 */
class HTML_QuickForm_option_orderer extends HTML_QuickForm_hidden
{

    /**
     * @var string[]
     */
    private ?array $options;

    private ?string $separator;

    /**
     * @param ?string $name
     * @param ?string $label
     * @param string[] $options
     * @param ?string $separator
     * @param ?array|?string $attributes Associative array of tag attributes or HTML attributes name="value" pairs
     */
    public function __construct(
        ?string $name = null, ?string $label = null, ?array $options = null, ?string $separator = '|',
        $attributes = null
    )
    {
        $this->separator = $separator;
        $value = ($_REQUEST[$name] ?? implode($this->separator, array_keys($options)));

        parent::__construct($name, $value, $attributes);

        $this->options = $options;
    }

    /**
     * Returns a 'safe' element's value
     *
     * @param array $submitValues array of submitted values to search
     * @param bool $assoc         whether to return the value as associative array
     */
    public function exportValue(array &$submitValues, bool $assoc = false)
    {
        return $this->getValue();
    }

    public function getFrozenHtml(): string
    {
        $html = [];

        $html[] = '<ol class="option-orderer oord-name_' . $this->getName() . '">';

        foreach ($this->getValue() as $index)
        {
            $html[] = '<li class="oord-value_' . $index . '">' . $this->options[$index] . '</li>';
        }

        $html[] = '</ol>';
        $html[] = parent::toHtml();

        return implode(PHP_EOL, $html);
    }

    public function getValue()
    {
        return explode($this->separator, parent::getValue());
    }

    public function toHtml(): string
    {
        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        /**
         * @var \Chamilo\Libraries\Format\Utilities\ResourceManager $resourceManager
         */
        $resourceManager = $container->get(ResourceManager::class);
        /**
         * @var \Chamilo\Libraries\File\WebPathBuilder $webPathBuilder
         */
        $webPathBuilder = $container->get(WebPathBuilder::class);

        $html = [];

        $html[] = $resourceManager->getResourceHtml(
            $webPathBuilder->getJavascriptPath('Chamilo\Libraries') . 'OptionOrderer.js'
        );
        $html[] = $this->getFrozenHtml();

        return implode(PHP_EOL, $html);
    }
}
