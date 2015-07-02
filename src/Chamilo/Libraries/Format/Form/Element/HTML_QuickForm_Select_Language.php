<?php


use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Configuration\Storage\DataManager;

/**
 *
 * @package common.html.formvalidator.Element
 */
/**
 * A dropdownlist with all languages to use with QuickForm
 */
class HTML_QuickForm_Select_Language extends HTML_QuickForm_select
{

    /**
     * Class constructor
     */
    public function HTML_QuickForm_Select_Language($elementName = null, $elementLabel = null, $options = null, $attributes = null)
    {
        parent :: __construct($elementName, $elementLabel, $options, $attributes);
        // Get all languages
        $languages = DataManager :: retrieves(Language :: class_name());

        $this->_options = array();
        $this->_values = array();

        while ($language = $languages->next_result())
        {
            $this->addOption($language->get_english_name(), $language->get_isocode());
        }
    }
}
