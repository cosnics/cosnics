<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface;
use Chamilo\Libraries\Storage\Service\ConditionTranslator;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Service\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class InConditionTranslator extends ConditionTranslator implements ConditionTranslatorInterface
{
    public function getConditionClassName(): string
    {
        return InCondition::class;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function translate(
        DBALQueryBuilder|ORMQueryBuilder $querybuilder, InCondition $inCondition, ?bool $enableAliasing = true
    ): string
    {
        $string = [];
        $values = $inCondition->getValues();
        $isArrayAndHasValues = is_array($values) && count($values) > 0;
        $isStaticConditionVariableAndHasValues =
            $values instanceof StaticConditionVariable && count($values->getValue()) > 0;
        $hasValues = $isArrayAndHasValues || $isStaticConditionVariableAndHasValues;

        if ($hasValues) {
            $string[] = $this->conditionVariableTranslatorRegistry->translate(
                $querybuilder, $inCondition->getConditionVariable(), $enableAliasing
            );
            $string[] = 'IN';

            if ($values instanceof StaticConditionVariable) {
                $type = $values->getType();
                $values = $values->getValue();
            }
            else {
                $type = ArrayParameterType::STRING;
            }

            $string[] = '(' . $querybuilder->createNamedParameter($values, $type) . ')';
        }
        else {
            $string[] = '1 = 0';
        }

        return implode(' ', $string);
    }
}
