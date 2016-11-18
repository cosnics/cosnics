<?php
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package common.html.formvalidator.Element
 */
/**
 * Form element to select a date and hour (with popup datepicker)
 */
class HTML_QuickForm_timepicker extends HTML_QuickForm_date
{

    private $include_minutes_picker;

    /**
     * Constructor
     */
    public function __construct($elementName = null, $elementLabel = null, $attributes = null, $include_minutes_picker = true)
    {
        if (! isset($attributes['form_name']))
        {
            return;
        }
        
        $js_form_name = $attributes['form_name'];
        // unset($attributes['form_name']);
        HTML_QuickForm_element::__construct($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_appendName = true;
        $this->_type = 'timepicker';
        $special_chars = array('D', 'l', 'd', 'M', 'F', 'm', 'y', 'H', 'a', 'A', 's', 'i', 'h', 'g', ' ');
        $hour_minute_devider = Translation::get('HourMinuteDivider', null, Utilities::COMMON_LIBRARIES);
        foreach ($special_chars as $index => $char)
        {
            $popup_link = str_replace($char, "\\" . $char, $popup_link);
            $hour_minute_devider = str_replace($char, "\\" . $char, $hour_minute_devider);
        }
        $editor_lang = Translation::getInstance()->getLanguageIsocode();
        if (empty($editor_lang))
        {
            // if there was no valid iso-code, use the english one
            $editor_lang = 'en';
        }
        // If translation not available in PEAR::HTML_QuickForm_date, add the Chamilo-translation
        if (! array_key_exists($editor_lang, $this->_locale))
        {
            $this->_locale[$editor_lang]['months_long'] = array(
                Translation::get("JanuaryLong", null, Utilities::COMMON_LIBRARIES), 
                Translation::get("FebruaryLong", null, Utilities::COMMON_LIBRARIES), 
                Translation::get("MarchLong", null, Utilities::COMMON_LIBRARIES), 
                Translation::get("AprilLong", null, Utilities::COMMON_LIBRARIES), 
                Translation::get("MayLong", null, Utilities::COMMON_LIBRARIES), 
                Translation::get("JuneLong", null, Utilities::COMMON_LIBRARIES), 
                Translation::get("JulyLong", null, Utilities::COMMON_LIBRARIES), 
                Translation::get("AugustLong", null, Utilities::COMMON_LIBRARIES), 
                Translation::get("SeptemberLong", null, Utilities::COMMON_LIBRARIES), 
                Translation::get("OctoberLong", null, Utilities::COMMON_LIBRARIES), 
                Translation::get("NovemberLong", null, Utilities::COMMON_LIBRARIES), 
                Translation::get("DecemberLong", null, Utilities::COMMON_LIBRARIES));
        }
        
        if ($include_minutes_picker)
        {
            $this->_options['format'] = 'H ' . $hour_minute_devider . ' i';
        }
        else
        {
            $this->_options['format'] = 'H' . $hour_minute_devider;
        }
        
        $this->_options['language'] = $editor_lang;
        $this->setValue(date('H'));
    }

    /**
     * HTML code to display this datepicker
     */
    public function toHtml()
    {
        $js = $this->getElementJS();
        return $js . parent::toHtml();
    }

    /**
     * Get the necessary javascript for this datepicker
     */
    public function getElementJS()
    {
        return ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries\Format', true) . 'TblChange.js');
    }

    /**
     * Export the date value in MySQL format
     * 
     * @return string YYYY-MM-DD HH:II:SS
     */
    public function exportValue()
    {
        $values = parent::getValue();
        $h = $values['H'][0];
        $i = $values['i'][0];
        $h = $h < 10 ? '0' . $h : $h;
        $i = $i < 10 ? '0' . $i : $i;
        
        if ($this->include_minutes_picker)
        {
            $datetime = $h . ':' . $i . ':00';
        }
        else
        {
            $datetime = $h;
        }
        
        $datetime = $h;
        
        $result[$this->getName()] = $datetime;
        return $result;
    }
}
