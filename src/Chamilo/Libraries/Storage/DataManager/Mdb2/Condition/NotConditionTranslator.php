<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2\Condition;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Mdb2\Condition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class NotConditionTranslator extends ConditionTranslator
{

    public function translate()
    {
        $string = array();
        
        $string[] = 'NOT (';
        $string[] = ConditionTranslator :: render($this->get_condition()->get_condition());
        $string[] = ')';
        
        return implode('', $string);
    }
}
