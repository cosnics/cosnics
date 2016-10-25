<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2\Condition;

use Chamilo\Libraries\Storage\DataManager\Mdb2\Database;
use Chamilo\Libraries\Storage\DataManager\Mdb2\Variable\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Mdb2\Condition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PatternMatchConditionTranslator extends ConditionTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Condition\ConditionTranslator::translate()
     */
    public function translate()
    {
        return ConditionVariableTranslator :: render($this->get_condition()->get_name()) . ' LIKE ' .
             Database :: quote($this->search_string($this->get_condition()->get_pattern()));
    }

    /**
     * Translates a string with wildcard characters "?" (single character) and "*" (any character sequence) to a SQL
     * pattern for use in a LIKE condition.
     * Should be suitable for any SQL flavor.
     *
     * @param string $string
     * @return string
     */
    public function search_string($string)
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
}
