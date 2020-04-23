<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 *
 * @package Chamilo\Libraries\Storage\Query
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ConditionTranslator extends ConditionPartTranslator
{

    /**
     * @return \Chamilo\Libraries\Storage\Query\ConditionPart
     */
    public function getCondition()
    {
        return $this->getConditionPart();
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     */
    public function setCondition(Condition $condition)
    {
        $this->setConditionPart($condition);
    }
}
