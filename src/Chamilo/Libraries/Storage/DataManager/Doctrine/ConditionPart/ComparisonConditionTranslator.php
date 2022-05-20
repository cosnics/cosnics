<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ComparisonConditionTranslator extends ConditionTranslator
{

    public function translate(
        ConditionPartTranslatorService $conditionPartTranslatorService, DataClassDatabaseInterface $dataClassDatabase,
        ComparisonCondition $comparisonCondition, ?bool $enableAliasing = true
    ): string
    {
        $translationParts = [];

        $translationParts[] = $conditionPartTranslatorService->translate(
            $dataClassDatabase, $comparisonCondition->getLeftConditionVariable(), $enableAliasing
        );

        if ($comparisonCondition->getOperator() == ComparisonCondition::EQUAL &&
            is_null($comparisonCondition->getRightConditionVariable()))
        {
            $translationParts[] = 'IS NULL';

            return implode(' ', $translationParts);
        }

        $translationParts[] = $this->translateOperator($comparisonCondition->getOperator());

        $translationParts[] = $conditionPartTranslatorService->translate(
            $dataClassDatabase, $comparisonCondition->getRightConditionVariable(), $enableAliasing
        );

        return implode(' ', $translationParts);
    }

    private function translateOperator(int $conditionOperator): string
    {
        switch ($conditionOperator)
        {
            case ComparisonCondition::GREATER_THAN :
                $translatedOperator = '>';
                break;
            case ComparisonCondition::GREATER_THAN_OR_EQUAL :
                $translatedOperator = '>=';
                break;
            case ComparisonCondition::LESS_THAN :
                $translatedOperator = '<';
                break;
            case ComparisonCondition::LESS_THAN_OR_EQUAL :
                $translatedOperator = '<=';
                break;
            case ComparisonCondition::EQUAL :
                $translatedOperator = '=';
                break;
            default :
                die('Unknown operator for Comparison condition');
        }

        return $translatedOperator;
    }
}
