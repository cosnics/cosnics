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
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translate($enableAliasing = true)
    {
        $className = $this->getConditionVariable()->get_class();

        if ($enableAliasing)
        {
            return $this->getDataClassDatabase()->getAlias($className::get_table_name()) . '.*';
        }
        else
        {
            return '*';
        }
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable
     */
    public function getConditionVariable()
    {
        return parent::getConditionVariable();
    }
}
