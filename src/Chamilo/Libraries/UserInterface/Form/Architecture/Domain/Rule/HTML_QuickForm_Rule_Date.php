<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Rule;

use HTML_QuickForm_Rule;

/**
 * QuickForm rule to check a date
 *
 * @package Chamilo\Libraries\Format\Form\Rule
 */
class HTML_QuickForm_Rule_Date extends HTML_QuickForm_Rule
{

    /**
     * Function to check a date
     *
     * @param string[] $value An array with keys F (month), d (day) and Y (year)
     *
     * @return bool True if date is valid
     */
    public function validate($value, $options = null): bool
    {
        return checkdate((int) $value['F'], (int) $value['d'], (int) $value['Y']);
    }
}
