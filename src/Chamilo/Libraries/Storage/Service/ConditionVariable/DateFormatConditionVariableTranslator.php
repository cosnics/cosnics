<?php
namespace Chamilo\Libraries\Storage\Service\ConditionVariable;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\DateFormatConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariableTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Service\ConditionVariable
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DateFormatConditionVariableTranslator extends ConditionVariableTranslator
    implements ConditionVariableTranslatorInterface
{
    public function getConditionVariableClassName(): string
    {
        return DateFormatConditionVariable::class;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException
     */
    public function translate(
        QueryBuilder $querybuilder, DateFormatConditionVariable $dateFormatConditionVariable,
        ?bool $enableAliasing = true
    ): string
    {
        $strings = [];

        $strings[] = 'FROM_UNIXTIME';

        $strings[] = '(';

        $strings[] = $this->getConditionVariableTranslatorCollection()->translate(
            $querybuilder, $dateFormatConditionVariable->getConditionVariable(), $enableAliasing
        );
        $strings[] = ', ';
        $strings[] = "'" . $dateFormatConditionVariable->getFormat() . "'";
        $strings[] = ')';

        if ($dateFormatConditionVariable->getAlias()) {
            return implode('', $strings) . ' AS ' . $dateFormatConditionVariable->getAlias();
        }
        else {
            return implode('', $strings);
        }
    }
}
