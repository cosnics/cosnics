<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Condition;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Condition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Replaced by the ConditionPartTranslators and related service and factory
 */
class NotConditionTranslator extends ConditionTranslator
{

    /**
     * @return string
     */
    public function translate()
    {
        $string = array();

        $string[] = 'NOT (';
        $string[] = ConditionTranslator::render($this->get_condition()->get_condition());
        $string[] = ')';

        return implode('', $string);
    }
}
