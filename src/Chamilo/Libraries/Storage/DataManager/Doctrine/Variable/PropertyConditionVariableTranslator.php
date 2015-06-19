<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Variable;

use Chamilo\Libraries\Storage\Cache\ConditionVariableCache;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Database;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertyConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Variable\ConditionVariableTranslator::translate()
     */
    public function translate()
    {
        if (! ConditionVariableCache :: exists($this->get_condition_variable()))
        {
            $value = Database :: escape_column_name(
                $this->get_condition_variable()->get_property(),
                $this->get_condition_variable()->get_alias());

            ConditionVariableCache :: set_cache($this->get_condition_variable(), $value);
        }

        return ConditionVariableCache :: get($this->get_condition_variable());
    }
}
