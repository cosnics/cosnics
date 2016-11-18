<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Cache\ConditionVariableCache;

/**
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ConditionVariableTranslator
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    private $condition_variable;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $condition_variable
     */
    public function __construct(ConditionVariable $condition_variable)
    {
        $this->condition_variable = $condition_variable;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    public function get_condition_variable()
    {
        return $this->condition_variable;
    }

    /**
     *
     * @return string
     */
    abstract public function translate();

    /**
     *
     * @param string $type
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $condition_variable
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariableTranslator
     */
    public static function factory($type, ConditionVariable $condition_variable)
    {
        $class = 'Chamilo\Libraries\Storage\DataManager\\' . $type . '\Variable\\' .
             ClassnameUtilities::getInstance()->getClassnameFromObject($condition_variable) . 'Translator';
        
        return new $class($condition_variable);
    }

    /**
     *
     * @param ConditionVariable $condition_variable
     * @return string
     */
    public static function render(ConditionVariable $conditionVariable)
    {
        $conditionVariableCache = ConditionVariableCache::getInstance();
        
        if (! $conditionVariableCache->exists($conditionVariable))
        {
            $conditionVariableCache->set($conditionVariable, static::runTranslator($conditionVariable));
        }
        
        return $conditionVariableCache->get($conditionVariable);
    }
}
