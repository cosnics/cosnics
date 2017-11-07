<?php
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
/**
 * Form element to select a date and hour (with popup datepicker)
 *
 * @package Chamilo\Libraries\Format\Form\Element
 */
class HTML_QuickForm_datepicker extends HTML_QuickForm_date
{

    /**
     *
     * @var boolean
     */
    private $include_time_picker;

    /**
     * Constructor
     */
    public function __construct($elementName = null, $elementLabel = null, $attributes = null, $include_time_picker = true)
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
        $this->_type = 'datepicker';

        $popup_link = '<a href="javascript:openCalendar(\'' . $js_form_name . '\',\'' . $elementName . '\')"><img src="' .
             Theme::getInstance()->getCommonImagePath('Action/CalendarSelect') . '" style="vertical-align:middle;"/></a>';
        $special_chars = array('D', 'l', 'd', 'M', 'F', 'm', 'y', 'H', 'a', 'A', 's', 'i', 'h', 'g', 'W', '.', ' ');
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

        $this->include_time_picker = $include_time_picker;

        if ($include_time_picker)
        {
            $this->_options['format'] = 'dFY ' . $popup_link . '   H ' . $hour_minute_devider . ' i';
        }
        else
        {
            $this->_options['format'] = 'dFY ' . $popup_link;
        }

        $this->_options['minYear'] = date('Y') - 5;
        $this->_options['maxYear'] = date('Y') + 10;
        $this->_options['language'] = $editor_lang;
        $this->setValue(date('Y-m-d H:i:s'));
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
     *
     * @return string
     */
    public function getElementJS()
    {
        $js = "\n";
        $js .= '<script src="';
        $js .= Path::getInstance()->getJavascriptPath('Chamilo\Libraries\Format', true) . 'TblChange.js';
        $js .= '" type="text/javascript"></script>';
        $js .= "\n";
        $js .= '<script type="text/javascript">';
        $js .= 'var path = \'' . Path::getInstance()->namespaceToFullPath('Chamilo\Configuration', true) . '\';' . "\n";
        $js .= 'var max_year="' . (date('Y') + 10) . '";';
        $js .= '</script>';
        return $js;
    }

    /**
     * Inheritance of setValue due to limitations of the date element When the default value is bigger then the maximum
     * possible selected value the default year is the lowest possible year.
     * This can give serious problems when using
     * multiple timestamps and compare
     *
     * @param string $value
     */
    public function setValue($value)
    {
        if (empty($value))
        {
            $value = array();
        }
        elseif (is_scalar($value))
        {
            if (! is_numeric($value))
            {
                $value = strtotime($value);
            }

            $year = date('Y', (int) $value);
            if ($year > $this->_options['maxYear'])
            {
                $value = mktime(23, 59, 59, 12, 31, $this->_options['maxYear']);
            }

            // might be a unix epoch, then we fill all possible values
            $arr = explode('-', date('w-j-n-Y-g-G-i-s-a-A-W', (int) $value));

            $value = array(
                'D' => $arr[0],
                'l' => $arr[0],
                'd' => $arr[1],
                'M' => $arr[2],
                'm' => $arr[2],
                'F' => $arr[2],
                'Y' => $arr[3],
                'y' => $arr[3],
                'h' => $arr[4],
                'g' => $arr[4],
                'H' => $arr[5],
                'i' => $this->_trimLeadingZeros($arr[6]),
                's' => $this->_trimLeadingZeros($arr[7]),
                'a' => $arr[8],
                'A' => $arr[9],
                'W' => $this->_trimLeadingZeros($arr[10]));
        }
        else
        {
            $value = array_map(array($this, '_trimLeadingZeros'), $value);
        }

        parent::setValue($value);
    }

    /**
     * Export the date value in MySQL format
     *
     * @return string YYYY-MM-DD HH:II:SS
     */
    public function exportValue()
    {
        $values = parent::getValue();
        $y = $values['Y'][0];
        $m = $values['F'][0];
        $d = $values['d'][0];
        $h = $values['H'][0];
        $i = $values['i'][0];
        $m = $m < 10 ? '0' . $m : $m;
        $d = $d < 10 ? '0' . $d : $d;
        $h = $h < 10 ? '0' . $h : $h;
        $i = $i < 10 ? '0' . $i : $i;

        if ($this->include_time_picker)
        {
            $datetime = $y . '-' . $m . '-' . $d . ' ' . $h . ':' . $i . ':00';
        }
        else
        {
            $datetime = $y . '-' . $m . '-' . $d;
        }

        if (strpos($this->getName(), '[') !== false)
        {
            parse_str($this->getName() . '=' . urlencode($datetime), $result);
        }
        else
        {
            $result[$this->getName()] = $datetime;
        }

        return $result;
    }
}
