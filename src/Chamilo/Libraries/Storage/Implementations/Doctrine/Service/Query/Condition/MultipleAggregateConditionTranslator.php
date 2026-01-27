<?php
namespace Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition;

use Chamilo\Libraries\Storage\Query\Condition\MultipleAggregateCondition;
use Chamilo\Libraries\Storage\Query\ConditionTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
abstract class MultipleAggregateConditionTranslator extends ConditionTranslator
{

    public function translate(
        QueryBuilder $querybuilder, MultipleAggregateCondition $multipleAggregateCondition, ?bool $enableAliasing = true
    ): string
    {
        $string = '';

        $conditionTranslations = [];

        foreach ($multipleAggregateCondition->getConditions() as $condition)
        {
            $translation = $this->getConditionPartTranslatorService()->translate(
                $querybuilder, $condition, $enableAliasing
            );

            if (!empty($translation))
            {
                $conditionTranslations[] = $translation;
            }
        }

        if (count($conditionTranslations) > 0)
        {
            $string = '(' . implode($multipleAggregateCondition->getOperator(), $conditionTranslations) . ')';
        }

        return $string;
    }
}
