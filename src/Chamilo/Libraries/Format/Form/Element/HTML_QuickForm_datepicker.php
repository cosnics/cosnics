<?php
namespace Chamilo\Libraries\Format\Form\Element;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use HTML_QuickForm_date;
use HTML_QuickForm_element;

/**
 * Form element to select a date and hour (with popup datepicker)
 *
 * @package Chamilo\Libraries\Format\Form\Element
 */
class HTML_QuickForm_datepicker extends HTML_QuickForm_date
{

    private ?string $formName;

    private ?bool $includeTimePicker;

    /**
     * @throws \Exception
     */
    public function __construct(
        ?string $formName = null, ?string $elementName = null, ?string $elementLabel = null, $attributes = null,
        ?bool $includeTimePicker = true
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
     * @param array $submitValues array of submitted values to search
     * @param bool $assoc         whether to return the value as associative array
     */
    public function exportValue(array &$submitValues, bool $assoc = false)
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

    public function getDateFormat(string $elementName, bool $includeTimePicker): string
    {
        $js_form_name = $this->formName;
        $glyph = new FontAwesomeGlyph('calendar-alt');

        $popupLink =
            '<a class="btn btn-default" href="javascript:openCalendar(\'' . $js_form_name . '\',\'' . $elementName .
            '\')">' . $glyph->render() . '</a>';
        $specialCharacters = ['D', 'l', 'd', 'M', 'F', 'm', 'y', 'H', 'a', 'A', 's', 'i', 'h', 'g', 'W', '.', ' '];
        $hourMinuteDivider = Translation::get('HourMinuteDivider', null, StringUtilities::LIBRARIES);

        foreach ($specialCharacters as $char)
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

            $value = [
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
            ];
        }
        else
        {
            $value = array_map([$this, '_trimLeadingZeros'], $value);
        }

        parent::setValue($value);
    }

    public function toHtml(): string
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
