<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

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
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPartTranslator::translate()
     */
    public function translate()
    {
        $strings = array();

        $strings[] = 'CASE ';

        foreach ($this->getConditionVariable()->get_case_elements() as $caseElement)
        {
            $strings[] = $this->getConditionPartTranslatorService()->translateConditionPart(
                $this->getDataClassDatabase(),
                $caseElement);
        }

        $strings[] = ' END';

        if ($this->getConditionVariable()->get_alias())
        {
            $value = implode(' ', $strings) . ' AS ' . $this->getConditionVariable()->get_alias();
        }
        else
        {
            $value = implode(' ', $strings);
        }

        return $value;
    }
}
