<?php
namespace Chamilo\Libraries\Format\Form\Element;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Format\Form\Element
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class HTML_QuickForm_toggle extends HTML_QuickForm_extended_checkbox
{

    /**
     * @param ?array|?string $attributes Associative array of tag attributes or HTML attributes name="value" pairs
     */
    public function __construct(
        ?string $elementName = null, ?string $elementLabel = null, string $text = '', $attributes = null,
        int $value = 1, ?string $return_value = null
    )
    {
        parent::__construct($elementName, $elementLabel, $text, $attributes, $value, $return_value);
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
        $html[] = ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(StringUtilities::LIBRARIES, true) . 'Toggle.js'
        );

        return implode(PHP_EOL, $html);
    }
}
