<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Variable;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Database;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Replaced by the ConditionPartTranslators and related service and factory
 */
class StaticConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     * @return string
     */
    public function translate()
    {
        $value = $this->get_condition_variable()->get_value();

        if ($this->get_condition_variable()->get_quote())
        {
            $value = Database::quote($value);
        }

        return $value;
    }
}
