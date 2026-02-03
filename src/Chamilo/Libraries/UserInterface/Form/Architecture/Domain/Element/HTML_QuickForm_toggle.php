<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element;

use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Service\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class HTML_QuickForm_toggle extends HTML_QuickForm_extended_checkbox
{
    use DependencyInjectionContainerTrait;

    public function __construct(
        ?string $elementName = null, ?string $elementLabel = null, string $text = '',
        null|array|string $attributes = null, int $value = 1, ?string $returnValue = null
    )
    {
        parent::__construct($elementName, $elementLabel, $text, $attributes, $value, $returnValue);
        $this->_type = 'toggle';
    }

    /**
     * @see HTML_QuickForm_extended_checkbox::getCheckboxClasses()
     */
    public function getCheckboxClasses(): string
    {
        return 'checkbox no-awesome-style';
    }

    public function toHtml(): string
    {
        $html = [];

        $html[] = parent::toHtml();
        $html[] = $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(StringUtilities::LIBRARIES) . 'Toggle.js'
        );

        return implode(PHP_EOL, $html);
    }
}
