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
     * @return \Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition
     */
    public function getCondition()
    {
        return parent::getCondition();
    }

    protected function getPattern(): string
    {
        return $this->searchString($this->getCondition()->get_pattern());
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
        // Escape SQL wildcard characters, thus prefixing %, ', \ and _ with a backslash
        $string = preg_replace(['/\\\\/', '/%/', '/\'/', '/_/'], ['\\\\\\\\', '\%', '\\\'', '\_'], $string);

        // Replace asterisks and question marks that are not prefixed with a backslash with the SQL equivalent
        return preg_replace(array('/(?<!\\\\)\*/', '/(?<!\\\\)\?/'), array('%', '_'), $string);
    }

    /**
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translate(bool $enableAliasing = true)
    {
        return $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $this->getCondition()->get_name(), $enableAliasing
            ) . ' LIKE ' . $this->getDataClassDatabase()->quote($this->getPattern());
    }
}
