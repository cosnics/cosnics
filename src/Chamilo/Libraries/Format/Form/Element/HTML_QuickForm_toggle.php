<?php
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Utilities\Utilities;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'HTML_QuickForm_extended_checkbox.php';

/**
 *
 * @package HTML_QuickForm_toggle
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HTML_QuickForm_toggle extends HTML_QuickForm_extended_checkbox
{

    public function __construct($elementName = null, $elementLabel = null, $text = '', $attributes = null, $value = 1, 
        $return_value = null)
    {
        parent::__construct($elementName, $elementLabel, $text, $attributes, $value, $return_value);
        $this->_type = 'toggle';
    }

    /**
     *
     * @see HTML_QuickForm_extended_checkbox::toHtml()
     */
    function toHtml()
    {
        $html = array();
        
        $html[] = parent::toHtml();
        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Utilities::COMMON_LIBRARIES, true) . 'Toggle.js');
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see HTML_QuickForm_extended_checkbox::getCheckboxClasses()
     */
    function getCheckboxClasses()
    {
        return 'checkbox no-awesome-style';
    }
}
