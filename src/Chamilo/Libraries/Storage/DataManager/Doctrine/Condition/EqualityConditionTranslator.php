<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Condition;

use Chamilo\Libraries\Storage\Cache\ConditionCache;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Variable\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Condition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
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
            if (! ConditionCache :: exists($this->get_condition()))
            {
                $value = ConditionVariableTranslator :: render(
                    $this->get_condition()->get_name()) . ' IS NULL';

                ConditionCache :: set_cache($this->get_condition(), $value);
            }

            return ConditionCache :: get($this->get_condition());
        }

        if (! ConditionCache :: exists($this->get_condition()))
        {
            $value = ConditionVariableTranslator :: render($this->get_condition()->get_name()) .
                 ' = ' . ConditionVariableTranslator :: render(
                    $this->get_condition()->get_value());
            ConditionCache :: set_cache($this->get_condition(), $value);
        }

        return ConditionCache :: get($this->get_condition());
    }
}
