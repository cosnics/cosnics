<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\RegularExpressionCondition;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface;
use Chamilo\Libraries\Storage\Service\ConditionTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Service\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class RegularExpressionConditionTranslator extends ConditionTranslator implements ConditionTranslatorInterface
{
    public function getConditionClassName(): string
    {
        return RegularExpressionCondition::class;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException
     */
    public function translate(
        QueryBuilder $querybuilder, RegularExpressionCondition $regularExpressionCondition, ?bool $enableAliasing = true
    ): string
    {
        $string = [];

        $string[] = $this->getConditionVariableTranslatorCollection()->translate(
            $querybuilder, $regularExpressionCondition->getConditionVariable(), $enableAliasing
        );
        $string[] = 'REGEXP';
        $string[] = $querybuilder->createNamedParameter($regularExpressionCondition->getRegularExpression());

        return implode(' ', $string);
    }
}
