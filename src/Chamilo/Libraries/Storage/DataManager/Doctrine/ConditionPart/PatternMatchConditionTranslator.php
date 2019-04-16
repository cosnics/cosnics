<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class PatternMatchConditionTranslator extends ConditionTranslator
{

    /**
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translate($enableAliasing = true)
    {
        return $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $this->getCondition()->get_name(), $enableAliasing
            ) . ' LIKE ' .
            $this->getDataClassDatabase()->quote($this->searchString($this->getCondition()->get_pattern()));
    }

    /**
     * Translates a string with wildcard characters "?" (single character) and "*" (any character sequence) to a SQL
     * pattern for use in a LIKE condition.
     * Should be suitable for any SQL flavor.
     *
     * @param string $string
     *
     * @return string
     */
    public function searchString($string)
    {
        /*
         * ====================================================================== A brief explanation of these regexps:
         * - The first one escapes SQL wildcard characters, thus prefixing %, ', \ and _ with a backslash. - The second
         * one replaces asterisks that are not prefixed with a backslash (which escapes them) with the SQL equivalent,
         * namely a percent sign. - The third one is similar to the second: it replaces question marks that are not
         * escaped with the SQL equivalent _. ======================================================================
         */
        $string = preg_replace_callback('/([%\'\\\\_])/e', "'\\\\\\\\' . '\\1'", $string);

        return preg_replace(array('/(?<!\\\\)\*/', '/(?<!\\\\)\?/'), array('%', '_'), $string);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition
     */
    public function getCondition()
    {
        return parent::getCondition();
    }
}
