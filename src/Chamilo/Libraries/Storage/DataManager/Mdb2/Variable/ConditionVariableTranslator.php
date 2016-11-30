<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2\Variable;

use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Mdb2\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ConditionVariableTranslator extends \Chamilo\Libraries\Storage\Query\Variable\ConditionVariableTranslator
{

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     * @return string
     */
    public static function runTranslator($conditionVariable)
    {
        return parent::factory(DataManager::TYPE_MDB2, $conditionVariable)->translate();
    }
}
