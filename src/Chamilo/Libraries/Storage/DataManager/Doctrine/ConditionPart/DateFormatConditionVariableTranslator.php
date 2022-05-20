<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\Query\Variable\DateFormatConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DateFormatConditionVariableTranslator extends ConditionVariableTranslator
{
    public function translate(
        ConditionPartTranslatorService $conditionPartTranslatorService, DataClassDatabaseInterface $dataClassDatabase,
        DateFormatConditionVariable $dateFormatConditionVariable, ?bool $enableAliasing = true
    ): string
    {
        $strings = [];

        $strings[] = 'FROM_UNIXTIME';

        $strings[] = '(';

        $strings[] = $conditionPartTranslatorService->translate(
            $dataClassDatabase, $dateFormatConditionVariable->getConditionVariable(), $enableAliasing
        );
        $strings[] = ', ';
        $strings[] = "'" . $dateFormatConditionVariable->getFormat() . "'";
        $strings[] = ')';

        if ($dateFormatConditionVariable->getAlias())
        {
            return implode('', $strings) . ' AS ' . $dateFormatConditionVariable->getAlias();
        }
        else
        {
            return implode('', $strings);
        }
    }
}
