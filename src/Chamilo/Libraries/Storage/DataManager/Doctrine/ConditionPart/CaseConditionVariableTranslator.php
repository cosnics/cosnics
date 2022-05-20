<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionPart;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\CaseConditionVariable
     */
    public function getConditionVariable(): ConditionPart
    {
        return parent::getConditionVariable();
    }

    public function translate(?bool $enableAliasing = true): string
    {
        $strings = [];

        $strings[] = 'CASE ';

        foreach ($this->getConditionVariable()->getCaseElementConditionVariables() as $caseElement)
        {
            $strings[] = $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $caseElement, $enableAliasing
            );
        }

        $strings[] = ' END';

        if ($this->getConditionVariable()->getAlias())
        {
            $value = implode(' ', $strings) . ' AS ' . $this->getConditionVariable()->getAlias();
        }
        else
        {
            $value = implode(' ', $strings);
        }

        return $value;
    }
}
