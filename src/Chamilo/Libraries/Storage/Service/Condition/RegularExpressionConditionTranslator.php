<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\RegularExpressionCondition;
use Chamilo\Libraries\Storage\Service\ConditionTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class RegularExpressionConditionTranslator extends ConditionTranslator
{
    public const CONDITION_CLASS = RegularExpressionCondition::class;

    public function translate(
        QueryBuilder $querybuilder, RegularExpressionCondition $regularExpressionCondition, ?bool $enableAliasing = true
    ): string
    {
        return $this->getConditionPartTranslatorService()->translate(
                $querybuilder, $regularExpressionCondition->getConditionVariable(), $enableAliasing
            ) . ' REGEXP ' . $querybuilder->createNamedParameter($regularExpressionCondition->getRegularExpression());
    }
}
