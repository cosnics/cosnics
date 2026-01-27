<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Service\ConditionTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class PatternMatchConditionTranslator extends ConditionTranslator
{
    public const CONDITION_CLASS = PatternMatchCondition::class;

    public function processPattern(string $pattern): string
    {
        // Escape SQL wildcard characters, thus prefixing %, ', \ and _ with a backslash
        $pattern = preg_replace(['/\\\\/', '/%/', '/\'/', '/_/'], ['\\\\\\\\', '\%', '\\\'', '\_'], $pattern);

        // Replace asterisks and question marks that are not prefixed with a backslash with the SQL equivalent
        return preg_replace(['/(?<!\\\\)\*/', '/(?<!\\\\)\?/'], ['%', '_'], $pattern);
    }

    public function translate(
        QueryBuilder $querybuilder, PatternMatchCondition $patternMatchCondition, ?bool $enableAliasing = true
    ): string
    {
        return $this->getConditionPartTranslatorService()->translate(
                $querybuilder, $patternMatchCondition->getConditionVariable(), $enableAliasing
            ) . ' LIKE ' .
            $querybuilder->createNamedParameter($this->processPattern($patternMatchCondition->getPattern()));
    }
}
