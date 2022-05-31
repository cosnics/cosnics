<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class InConditionTranslator extends ConditionTranslator
{

    public function translate(
        ConditionPartTranslatorService $conditionPartTranslatorService, DataClassDatabaseInterface $dataClassDatabase,
        InCondition $inCondition, ?bool $enableAliasing = true
    ): string
    {
        $values = $inCondition->getValues();

        if (count($values) > 0)
        {
            $where_clause = [];

            $where_clause[] = $conditionPartTranslatorService->translate(
                    $dataClassDatabase, $inCondition->getConditionVariable(), $enableAliasing
                ) . ' IN (';

            $placeholders = [];

            foreach ($values as $value)
            {
                $placeholders[] = $dataClassDatabase->quote($value);
            }

            $where_clause[] = implode(',', $placeholders);
            $where_clause[] = ')';

            $value = implode('', $where_clause);
        }
        else
        {
            $value = '1 = 0';
        }

        return $value;
    }
}
