<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Variable;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Variable
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Replaced by the ConditionPartTranslators and related service and factory
 */
class PropertiesConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     * @return string
     */
    public function translate()
    {
        return $this->get_condition_variable()->get_alias() . '.*';
    }
}
