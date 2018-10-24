<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertiesConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPartTranslator::translate()
     */
    public function translate()
    {
        $className = $this->getPropertiesConditionVariable()->get_class();
        return $this->getDataClassDatabase()->getAlias($className::get_table_name()) . '.*';
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable
     */
    public function getPropertiesConditionVariable()
    {
        return $this->getConditionVariable();
    }
}
