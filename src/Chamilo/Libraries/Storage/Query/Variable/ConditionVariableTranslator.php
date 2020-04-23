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
    private $conditionVariable;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     */
    public function __construct(ConditionVariable $conditionVariable)
    {
        $this->conditionVariable = $conditionVariable;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     *
     * @return string
     */
    public static function render(ConditionVariable $conditionVariable)
    {
        $conditionVariableCache = ConditionVariableCache::getInstance();

        if (!$conditionVariableCache->exists($conditionVariable))
        {
            $conditionVariableCache->set($conditionVariable, static::runTranslator($conditionVariable));
        }

        return $conditionVariableCache->get($conditionVariable);
    }

    /**
     *
     * @param string $type
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariableTranslator
     * @throws \ReflectionException
     */
    public static function factory($type, ConditionVariable $conditionVariable)
    {
        $class = 'Chamilo\Libraries\Storage\DataManager\\' . $type . '\Variable\\' .
            ClassnameUtilities::getInstance()->getClassnameFromObject($conditionVariable) . 'Translator';

        return new $class($conditionVariable);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    public function get_condition_variable()
    {
        return $this->conditionVariable;
    }

    /**
     *
     * @return string
     */
    abstract public function translate();
}
