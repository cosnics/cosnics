<?php
namespace Chamilo\Libraries\Storage\Service\ConditionVariable;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\DateFormatConditionVariable;
use Chamilo\Libraries\Storage\Service\ConditionVariableTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Variable
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DateFormatConditionVariableTranslator extends ConditionVariableTranslator
{
    public const CONDITION_CLASS = DateFormatConditionVariable::class;

    public function translate(
        QueryBuilder $querybuilder, DateFormatConditionVariable $dateFormatConditionVariable,
        ?bool $enableAliasing = true
    ): string
    {
        $strings = [];

        $strings[] = 'FROM_UNIXTIME';

        $strings[] = '(';

        $strings[] = $this->getConditionPartTranslatorService()->translate(
            $querybuilder, $dateFormatConditionVariable->getConditionVariable(), $enableAliasing
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
