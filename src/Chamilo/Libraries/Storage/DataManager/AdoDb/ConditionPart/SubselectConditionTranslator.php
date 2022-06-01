<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SubselectConditionTranslator extends ConditionTranslator
{
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

        $string[] = $class::getStorageUnitName();

        $string[] = 'AS';
        $string[] = $this->getStorageAliasGenerator()->getDataClassAlias($class);

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