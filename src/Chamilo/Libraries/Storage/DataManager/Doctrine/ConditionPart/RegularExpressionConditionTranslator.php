<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\Condition\RegularExpressionCondition;
use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class RegularExpressionConditionTranslator extends ConditionTranslator
{
    public const CONDITION_CLASS = RegularExpressionCondition::class;

    public function translate(
        DataClassDatabaseInterface $dataClassDatabase, RegularExpressionCondition $regularExpressionCondition,
        ?bool $enableAliasing = true
    ): string
    {
        return $this->getConditionPartTranslatorService()->translate(
                $dataClassDatabase, $regularExpressionCondition->getConditionVariable(), $enableAliasing
            ) . ' REGEXP ' . $dataClassDatabase->quote($regularExpressionCondition->getRegularExpression());
    }
}
