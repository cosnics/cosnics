<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Storage\Query
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ConditionVariableTranslator extends ConditionPartTranslator
{

    public function getConditionVariable(): ConditionPart
    {
        return $this->getConditionPart();
    }
    
    public function setConditionVariable(ConditionVariable $conditionVariable)
    {
        $this->setConditionPart($conditionVariable);
    }
}
