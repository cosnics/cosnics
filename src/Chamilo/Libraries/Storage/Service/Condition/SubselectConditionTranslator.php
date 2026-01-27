<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Service\ConditionTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class SubselectConditionTranslator extends ConditionTranslator
{
    public const CONDITION_CLASS = SubselectCondition::class;

    public function translate(
        QueryBuilder $querybuilder, SubselectCondition $subselectCondition, ?bool $enableAliasing = true
    ): string
    {
        $string = [];

        $string[] = $this->getConditionPartTranslatorService()->translate(
            $querybuilder, $subselectCondition->getConditionVariable(), $enableAliasing
        );

        $string[] = 'IN (';
        $string[] = 'SELECT';

        $string[] = $this->getConditionPartTranslatorService()->translate(
            $querybuilder, $subselectCondition->getSubselectConditionVariable(), $enableAliasing
        );

        $string[] = 'FROM';

        /**
         * @var class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass> $class
         */
        $class = $subselectCondition->getSubselectConditionVariable()->getDataClassName();

        $string[] = $class::getStorageUnitName();
        $string[] = 'AS';
        $string[] = $this->getStorageAliasGenerator()->getDataClassAlias($class);

        if ($subselectCondition->getCondition())
        {
            $string[] = 'WHERE ';
            $string[] = $this->getConditionPartTranslatorService()->translate(
                $querybuilder, $subselectCondition->getCondition(), $enableAliasing
            );
        }

        $string[] = ')';

        return implode(' ', $string);
    }
}