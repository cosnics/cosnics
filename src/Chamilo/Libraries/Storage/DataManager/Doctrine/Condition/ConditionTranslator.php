<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Condition;

use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Condition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Replaced by the ConditionPartTranslators and related service and factory
 */
abstract class ConditionTranslator extends \Chamilo\Libraries\Storage\Query\Condition\ConditionTranslator
{

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     * @return string
     */
    public static function runTranslator($conditionVariable)
    {
        return parent::factory(DataManager::TYPE_DOCTRINE, $conditionVariable)->translate();
    }
}
