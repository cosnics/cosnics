<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\Condition\MultipleAggregateCondition;
use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class MultipleAggregateConditionTranslator extends ConditionTranslator
{

    public function translate(
        ConditionPartTranslatorService $conditionPartTranslatorService, DataClassDatabaseInterface $dataClassDatabase,
        MultipleAggregateCondition $multipleAggregateCondition, ?bool $enableAliasing = true
    ): string
    {
        $string = '';

        $conditionTranslations = [];
        $count = 0;

        foreach ($multipleAggregateCondition->getConditions() as $key => $condition)
        {
            $count ++;
            $translation = $conditionPartTranslatorService->translate(
                $dataClassDatabase, $condition, $enableAliasing
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
