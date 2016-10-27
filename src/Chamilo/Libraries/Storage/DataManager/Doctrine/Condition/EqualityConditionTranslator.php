<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Condition;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Variable\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Condition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Replaced by the ConditionPartTranslators and related service and factory
 */
class EqualityConditionTranslator extends ConditionTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Condition\ConditionTranslator::translate()
     */
    public function translate()
    {
        if (is_null($this->get_condition()->get_value()))
        {
            return ConditionVariableTranslator::render($this->get_condition()->get_name()) . ' IS NULL';
        }

        return ConditionVariableTranslator::render($this->get_condition()->get_name()) . ' = ' . ConditionVariableTranslator::render(
            $this->get_condition()->get_value());
    }
}
