<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertyConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPartTranslator::translate()
     */
    public function translate()
    {
        $className = $this->getPropertyConditionVariable()->get_class();
        $alias = $this->getDataClassDatabase()->getAlias($className::get_table_name());

        return $this->getDataClassDatabase()->escapeColumnName(
            $this->getPropertyConditionVariable()->get_property(),
            $alias);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable
     */
    public function getPropertyConditionVariable()
    {
        return $this->getConditionVariable();
    }
}
