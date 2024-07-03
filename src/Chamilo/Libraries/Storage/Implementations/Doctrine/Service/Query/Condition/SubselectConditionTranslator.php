<?php
namespace Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition;

use Chamilo\Libraries\Storage\Architecture\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class SubselectConditionTranslator extends ConditionTranslator
{
    public const CONDITION_CLASS = SubselectCondition::class;

    public function translate(
        DataClassDatabaseInterface $dataClassDatabase, SubselectCondition $subselectCondition,
        ?bool $enableAliasing = true
    ): string
    {
        $string = [];

        $string[] = $this->getConditionPartTranslatorService()->translate(
            $dataClassDatabase, $subselectCondition->getConditionVariable(), $enableAliasing
        );

        $string[] = 'IN (';
        $string[] = 'SELECT';

        $string[] = $this->getConditionPartTranslatorService()->translate(
            $dataClassDatabase, $subselectCondition->getSubselectConditionVariable(), $enableAliasing
        );

        $string[] = 'FROM';

        /**
         * @var class-string<\Chamilo\Libraries\Storage\DataClass\DataClass> $class
         */
        $class = $subselectCondition->getSubselectConditionVariable()->getDataClassName();

        $string[] = $class::getStorageUnitName();
        $string[] = 'AS';
        $string[] = $this->getStorageAliasGenerator()->getDataClassAlias($class);

        if ($subselectCondition->getCondition())
        {
            $string[] = 'WHERE ';
            $string[] = $this->getConditionPartTranslatorService()->translate(
                $dataClassDatabase, $subselectCondition->getCondition(), $enableAliasing
            );
        }

        $string[] = ')';

        return implode(' ', $string);
    }
}