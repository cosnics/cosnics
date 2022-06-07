<?php

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

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
    private $includeTimePicker;

    /**
     * @var string
     */
    private $formName;

    /**
     * HTML_QuickForm_datepicker constructor.
     *
     * @param string $formName
     * @param string $elementName
     * @param string $elementLabel
     * @param string $attributes
     * @param boolean $includeTimePicker
     */
    public function __construct(
        $formName = null, $elementName = null, $elementLabel = null, $attributes = null, $includeTimePicker = true
    )
    {
        if (!isset($formName))
        {
            return;
        }

        $attributes = $this->addFormControlToElementAttributes($attributes);

        HTML_QuickForm_element::__construct($elementName, $elementLabel, $attributes);

        $this->_persistantFreeze = true;
        $this->_appendName = true;
        $this->_type = 'datepicker';
        $this->includeTimePicker = $includeTimePicker;
        $this->formName = $formName;

        $this->_options['format'] = $this->getDateFormat($elementName, $includeTimePicker);
        $this->_options['minYear'] = date('Y') - 5;
        $this->_options['maxYear'] = date('Y') + 10;
        $this->_options['language'] = Translation::getInstance()->getLanguageIsocode();

        $this->setValue(date('Y-m-d H:i:s'));
    }

    /**
     * @param string[] $attributes
     *
     * @return string[]
     */
    protected function addFormControlToElementAttributes($attributes)
    {
        if (is_array($attributes))
        {
            if (!array_key_exists('class', $attributes))
            {
                $attributes['class'] = 'form-control';
            }
            else
            {
                $classAttributes = $attributes['class'];

                if (!is_array($classAttributes))
                {
                    $classAttributes = explode(' ', $classAttributes);
                }

                if (!in_array('form-control', $classAttributes))
                {
                    array_unshift($classAttributes, 'form-control');
                }

                $attributes['class'] = implode(' ', $classAttributes);
            }
        }

        return $attributes;
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

        if ($this->includeTimePicker)
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

    /**
     * @param string $elementName
     * @param boolean $includeTimePicker
     *
     * @return string
     */
    public function getDateFormat($elementName, $includeTimePicker)
    {
        $js_form_name = $this->formName;
        $glyph = new FontAwesomeGlyph('calendar-alt');

        $popupLink =
            '<a class="btn btn-default" href="javascript:openCalendar(\'' . $js_form_name . '\',\'' . $elementName .
            '\')">' . $glyph->render() . '</a>';
        $specialCharacters = array('D', 'l', 'd', 'M', 'F', 'm', 'y', 'H', 'a', 'A', 's', 'i', 'h', 'g', 'W', '.', ' ');
        $hourMinuteDivider = Translation::get('HourMinuteDivider', null, StringUtilities::LIBRARIES);

        foreach ($specialCharacters as $index => $char)
        {
            $popupLink = str_replace($char, "\\" . $char, $popupLink);
            $hourMinuteDivider = str_replace($char, "\\" . $char, $hourMinuteDivider);
        }

        if ($includeTimePicker)
        {
            return 'd F Y   ' . $popupLink . '   H ' . $hourMinuteDivider . ' i';
        }
        else
        {
            return 'd F Y   ' . $popupLink;
        }
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
            $value = [];
        }
        elseif (is_scalar($value))
        {
            if (!is_numeric($value))
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
                'W' => $this->_trimLeadingZeros($arr[10])
            );
        }
        else
        {
            $value = array_map(array($this, '_trimLeadingZeros'), $value);
        }

        parent::setValue($value);
    }

    /**
     * HTML code to display this datepicker
     */
    public function toHtml()
    {
        $pathBuilder = Path::getInstance();
        $resourceManager = ResourceManager::getInstance();

        $html = [];

        $html[] = $resourceManager->getResourceHtml(
            $pathBuilder->getJavascriptPath('Chamilo\Libraries\Format', true) . 'TblChange.js'
        );
        $html[] = '<script>';
        $html[] = 'var max_year="' . (date('Y') + 10) . '";';
        $html[] = '</script>';
        $html[] = parent::toHtml();

        return implode(PHP_EOL, $html);
    }
}
