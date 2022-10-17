<?php
namespace Chamilo\Libraries\Format\Form\Rule;

use HTML_QuickForm_Rule_Compare;

/**
 * QuickForm rule to compare 2 dates
 *
 * @package Chamilo\Libraries\Format\Form\Rule
 */
class HTML_QuickForm_Rule_DateCompare extends HTML_QuickForm_Rule_Compare
{

    /**
     * Validate 2 dates
     *
     * @param string[][] $values Array with the 2 dates. Each element in this array
     *                           should be an array width keys F (month), d (day) and Y (year)
     * @param string $operator   The operator to use (default '==')
     *
     * @return bool True if the 2 given dates match the operator
     */
    public function validate($values, $operator = null): bool
    {
        $date1 = $values[0];
        $date2 = $values[1];

        $time1 = mktime($date1['H'], $date1['i'], 0, $date1['F'], $date1['d'], $date1['Y']);
        $time2 = mktime($date2['H'], $date2['i'], 0, $date2['F'], $date2['d'], $date2['Y']);

        return parent::validate([$time1, $time2], $operator);
    }
}
