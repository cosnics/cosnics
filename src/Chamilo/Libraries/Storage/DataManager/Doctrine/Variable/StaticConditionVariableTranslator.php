<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Variable;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Database;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class StaticConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Variable\ConditionVariableTranslator::translate()
     */
    public function translate()
    {
        $value = $this->get_condition_variable()->get_value();

        if ($this->get_condition_variable()->get_quote())
        {
            $value = Database :: quote($value);
        }

        return $value;
    }
}
