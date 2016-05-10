<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2\Condition;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Mdb2\Condition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AggregateConditionTranslator extends ConditionTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Condition\ConditionTranslator::translate()
     */
    public function translate()
    {
        $string = '';
        
        $condition_translations = array();
        $count = 0;
        
        foreach ($this->get_condition()->get_conditions() as $key => $condition)
        {
            $count ++;
            $translation = ConditionTranslator :: render($condition);
            
            if (! empty($translation))
            {
                $condition_translations[] = $translation;
            }
        }
        
        if (count($condition_translations) > 0)
        {
            $string = '(' . implode($this->get_condition()->get_operator(), $condition_translations) . ')';
        }
        
        return $string;
    }
}
