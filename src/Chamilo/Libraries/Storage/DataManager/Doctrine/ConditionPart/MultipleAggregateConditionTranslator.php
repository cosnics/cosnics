<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\Condition\MultipleAggregateCondition;
use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
abstract class MultipleAggregateConditionTranslator extends ConditionTranslator
{

    public function translate(
        DataClassDatabaseInterface $dataClassDatabase, MultipleAggregateCondition $multipleAggregateCondition,
        ?bool $enableAliasing = true
    ): string
    {
        $string = '';

        $conditionTranslations = [];

        foreach ($multipleAggregateCondition->getConditions() as $condition)
        {
            $translation = $this->getConditionPartTranslatorService()->translate(
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
