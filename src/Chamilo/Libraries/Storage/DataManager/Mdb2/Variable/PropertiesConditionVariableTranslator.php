<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2\Variable;

/**
 * Translation of ConditionVariables in the context of an MDB2-based storage layer
 * 
 * @package common.libraries
 * @author Sven Vanpoucke
 */
class PropertiesConditionVariableTranslator extends ConditionVariableTranslator
{

    public function translate()
    {
        return $this->get_condition_variable()->get_alias() . '.*';
    }
}
