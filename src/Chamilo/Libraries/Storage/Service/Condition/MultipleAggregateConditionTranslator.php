<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\MultipleAggregateCondition;
use Chamilo\Libraries\Storage\Service\ConditionTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Service\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
abstract class MultipleAggregateConditionTranslator extends ConditionTranslator
{
    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function translate(
        QueryBuilder $querybuilder, MultipleAggregateCondition $multipleAggregateCondition, ?bool $enableAliasing = true
    ): string
    {
        if (!empty($multipleAggregateCondition->getConditions())) {
            $string = [];

            foreach ($multipleAggregateCondition->getConditions() as $condition) {
                $translation =
                    $this->getConditionTranslatorCollection()->translate($querybuilder, $condition, $enableAliasing);

                if (!empty($translation)) {
                    $string[] = $translation;
                }
            }

            if (count($string) > 0) {
                return '(' . implode($multipleAggregateCondition->getOperator(), $string) . ')';
            }
        }

        return '';
    }
}
