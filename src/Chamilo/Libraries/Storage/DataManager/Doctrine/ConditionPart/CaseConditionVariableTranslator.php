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
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translate($enableAliasing = true)
    {
        $strings = array();

        $strings[] = 'CASE ';

        foreach ($this->getConditionVariable()->get_case_elements() as $caseElement)
        {
            $strings[] = $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $caseElement, $enableAliasing
            );
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

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\CaseConditionVariable
     */
    public function getConditionVariable()
    {
        return parent::getConditionVariable();
    }
}
