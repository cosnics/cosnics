<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Service\ConditionTranslator;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class InConditionTranslator extends ConditionTranslator
{
    public const CONDITION_CLASS = InCondition::class;

    public function translate(
        QueryBuilder $querybuilder, InCondition $inCondition, ?bool $enableAliasing = true
    ): string
    {
        $values = $inCondition->getValues();

        if (count($values) > 0)
        {
            $where_clause = [];

            $where_clause[] = $this->getConditionPartTranslatorService()->translate(
                    $querybuilder, $inCondition->getConditionVariable(), $enableAliasing
                ) . ' IN (';

            $where_clause[] = $querybuilder->createNamedParameter($values, ArrayParameterType::STRING);
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
