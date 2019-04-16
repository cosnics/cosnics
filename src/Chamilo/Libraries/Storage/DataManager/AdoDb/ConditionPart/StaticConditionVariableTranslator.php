<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class StaticConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translate(bool $enableAliasing = true)
    {
        $value = $this->getConditionVariable()->get_value();

        if ($this->getConditionVariable()->get_quote())
        {
            $value = $this->getDataClassDatabase()->quote($value);
        }

        return $value;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable
     */
    public function getConditionVariable()
    {
        return parent::getConditionVariable();
    }
}
