<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Cache\ConditionCache;

/**
 *
 * @package Chamilo\Libraries\Storage\Query\Condition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ConditionTranslator
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    private $condition;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     */
    public function __construct(Condition $condition)
    {
        $this->condition = $condition;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_condition()
    {
        return $this->condition;
    }

    /**
     *
     * @param string $type
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return \Chamilo\Libraries\Storage\Query\Condition\ConditionTranslator
     */
    public static function factory($type, Condition $condition)
    {
        $class = 'Chamilo\Libraries\Storage\DataManager\\' . $type . '\Condition\\' .
             ClassnameUtilities :: getInstance()->getClassnameFromObject($condition) . 'Translator';

        return new $class($condition);
    }

    /**
     *
     * @return string
     */
    abstract public function translate();

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     */
    public static function render(Condition $condition)
    {
        $queryCacheEnabled = Configuration :: get_instance()->get_setting(
            array('Chamilo\Configuration', 'debug', 'enable_query_cache'));

        if ($queryCacheEnabled)
        {
            $conditionCache = ConditionCache :: getInstance();

            if (! $conditionCache->exists($condition))
            {
                $conditionCache->set($condition, static :: runTranslator($condition));
            }

            return $conditionCache->get($condition);
        }
        else
        {
            return static :: runTranslator($condition);
        }
    }
}
