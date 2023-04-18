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
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPartTranslator::translate()
     */
    public function translate()
    {
        return $this->getConditionPartTranslatorService()->translateConditionPart(
            $this->getDataClassDatabase(),
            $this->getCondition()->get_name()) . ' LIKE ' .
             $this->getDataClassDatabase()->quote($this->searchString($this->getCondition()->get_pattern()));
    }

    /**
     * Translates a string with wildcard characters "?" (single character) and "*" (any character sequence) to a SQL
     * pattern for use in a LIKE condition.
     * Should be suitable for any SQL flavor.
     *
     * @param string $string
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
        $string = preg_replace(['/\\\\/', '/%/', '/\'/', '/_/'], ['\\\\\\\\', '\%', '\\\'', '\_'], $string);

        // Replace asterisks and question marks that are not prefixed with a backslash with the SQL equivalent
        return preg_replace(['/(?<!\\\\)\*/', '/(?<!\\\\)\?/'], ['%', '_'], $string);
    }
}
