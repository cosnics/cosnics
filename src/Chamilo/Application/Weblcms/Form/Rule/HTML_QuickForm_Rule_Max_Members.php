<?php
namespace Chamilo\Application\Weblcms\Form\Rule;

use HTML_QuickForm_Rule;

/**
 *
 * @package common.html.formvalidator.Rule
 */
/**
 * QuickForm rule to check a date
 */
class HTML_QuickForm_Rule_Max_Members extends HTML_QuickForm_Rule
{
    const UNLIMITED_MEMBERS = 'unlimited_members';

    public function validate($values, $options = null): bool
    {
        if ($values[0][self::UNLIMITED_MEMBERS] || (is_numeric($values[1]) && $values[1] > 0))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
