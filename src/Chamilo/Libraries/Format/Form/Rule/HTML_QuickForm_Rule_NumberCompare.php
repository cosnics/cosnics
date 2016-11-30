<?php
/**
 *
 * @package common.html.formvalidator.Rule
 */
/**
 * QuickForm rule to compare a number to a predefined value
 */
class HTML_QuickForm_Rule_NumberCompare extends HTML_QuickForm_Rule_Compare
{

    public function validate($fields, $operator = null, $value)
    {
        $field = $fields[0];
        return parent::validate(array($field, $value), $operator);
    }
}
