<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface;
use Chamilo\Libraries\Storage\Service\ConditionTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Service\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class PatternMatchConditionTranslator extends ConditionTranslator implements ConditionTranslatorInterface
{
    public function getConditionClassName(): string
    {
        return PatternMatchCondition::class;
    }

    public function processPattern(string $pattern): string
    {
        // Escape SQL wildcard characters, thus prefixing %, ', \ and _ with a backslash
        $pattern = preg_replace(['/\\\\/', '/%/', '/\'/', '/_/'], ['\\\\\\\\', '\%', '\\\'', '\_'], $pattern);

        // Replace asterisks and question marks that are not prefixed with a backslash with the SQL equivalent
        return preg_replace(['/(?<!\\\\)\*/', '/(?<!\\\\)\?/'], ['%', '_'], $pattern);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function translate(
        QueryBuilder $querybuilder, PatternMatchCondition $patternMatchCondition, ?bool $enableAliasing = true
    ): string
    {
        $string = [];

        $string[] = $this->conditionVariableTranslatorRegistry->translate(
            $querybuilder, $patternMatchCondition->getConditionVariable(), $enableAliasing
        );
        $string[] = 'LIKE';
        $string[] = $querybuilder->createNamedParameter($this->processPattern($patternMatchCondition->getPattern()));

        return implode(' ', $string);
    }
}
