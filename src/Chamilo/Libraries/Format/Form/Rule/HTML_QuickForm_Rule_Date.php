<?php
namespace Chamilo\Libraries\Format\Form\Rule;

/**
 * QuickForm rule to check a date
 *
 * @package Chamilo\Libraries\Format\Form\Rule
 */
class HTML_QuickForm_Rule_Date extends \HTML_QuickForm_Rule
{

    /**
     * Function to check a date
     *
     * @param string[] $date An array with keys F (month), d (day) and Y (year)
     * @return boolean True if date is valid
     */
    public function validate($date, $options = null): bool
    {
        return checkdate($date['F'], $date['d'], $date['Y']);
    }
}
