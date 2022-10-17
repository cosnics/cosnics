<?php
namespace Chamilo\Libraries\Format\Form\Rule;

use HTML_QuickForm_Rule_Compare;

/**
 * QuickForm rule to compare a number to a predefined value
 *
 * @package Chamilo\Libraries\Format\Form\Rule
 */
class HTML_QuickForm_Rule_NumberCompare extends HTML_QuickForm_Rule_Compare
{

    /**
     * @param string[] $value
     * @param ?string $options
     * @param string $compare
     */
    public function validate($value, $options = null, $compare): bool
    {
        $field = $value[0];

        return parent::validate([$field, $compare], $options);
    }
}
