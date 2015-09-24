<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Condition;

use Chamilo\Libraries\Storage\Cache\ConditionCache;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Condition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class NotConditionTranslator extends ConditionTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Condition\ConditionTranslator::translate()
     */
    public function translate()
    {
        if (! ConditionCache :: exists($this->get_condition()))
        {
            $string = array();

            $string[] = 'NOT (';
            $string[] = ConditionTranslator :: render($this->get_condition()->get_condition());
            $string[] = ')';

            ConditionCache :: set_cache($this->get_condition(), implode('', $string));
        }

        return ConditionCache :: get($this->get_condition());
    }
}
