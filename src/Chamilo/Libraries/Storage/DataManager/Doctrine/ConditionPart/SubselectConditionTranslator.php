<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SubselectConditionTranslator extends ConditionTranslator
{

    /**
     * @throws \ReflectionException
     */
    public function translate(
        ConditionPartTranslatorService $conditionPartTranslatorService, DataClassDatabaseInterface $dataClassDatabase,
        SubselectCondition $subselectCondition, ?bool $enableAliasing = true
    ): string
    {
        $string = [];

        $string[] = $conditionPartTranslatorService->translate(
            $dataClassDatabase, $subselectCondition->getConditionVariable(), $enableAliasing
        );

        $string[] = 'IN (';
        $string[] = 'SELECT';

        $string[] = $conditionPartTranslatorService->translate(
            $dataClassDatabase, $subselectCondition->getSubselectConditionVariable(), $enableAliasing
        );

        $string[] = 'FROM';

        $class = $subselectCondition->getSubselectConditionVariable()->getDataClassName();

        $alias = $this->getStorageAliasGenerator()->getDataClassAlias($class);

        $string[] = $class::getTableName();

        $string[] = 'AS';
        $string[] = $alias;

        if ($subselectCondition->getCondition())
        {
            $string[] = 'WHERE ';
            $string[] = $conditionPartTranslatorService->translate(
                $dataClassDatabase, $subselectCondition->getCondition(), $enableAliasing
            );
        }

        $string[] = ')';

        return implode(' ', $string);
    }
}