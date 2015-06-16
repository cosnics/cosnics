<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2\Variable;

use Chamilo\Libraries\Storage\DataManager\Mdb2\Database;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Mdb2\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class StaticConditionVariableTranslator extends ConditionVariableTranslator
{

    public function translate()
    {
        $value = $this->get_condition_variable()->get_value();

        if ($this->get_condition_variable()->get_quote())
        {
            return Database :: quote($value);
        }

        return $value;
    }
}
