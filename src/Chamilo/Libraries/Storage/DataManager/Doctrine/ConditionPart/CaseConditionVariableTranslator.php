<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\Query\Variable\CaseConditionVariable;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseConditionVariableTranslator extends ConditionVariableTranslator
{
    public const CONDITION_CLASS = CaseConditionVariable::class;

    public function translate(
        DataClassDatabaseInterface $dataClassDatabase, CaseConditionVariable $caseConditionVariable,
        ?bool $enableAliasing = true
    ): string
    {
        $strings = [];

        $strings[] = 'CASE ';

        foreach ($caseConditionVariable->get() as $caseElement)
        {
            $strings[] = $this->getConditionPartTranslatorService()->translate(
                $dataClassDatabase, $caseElement, $enableAliasing
            );
        }

        $strings[] = ' END';

        if ($caseConditionVariable->getAlias())
        {
            $value = implode(' ', $strings) . ' AS ' . $caseConditionVariable->getAlias();
        }
        else
        {
            $value = implode(' ', $strings);
        }

        return $value;
    }
}
