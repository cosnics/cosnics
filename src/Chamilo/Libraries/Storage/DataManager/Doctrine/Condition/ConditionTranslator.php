<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Condition;

use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Condition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ConditionTranslator extends \Chamilo\Libraries\Storage\Query\Condition\ConditionTranslator
{

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     */
    public static function factory(Condition $condition)
    {
        return parent :: factory(DataManager :: TYPE_DOCTRINE, $condition);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     */
    public static function render(Condition $condition)
    {
        return parent :: render(DataManager :: TYPE_DOCTRINE, $condition);
    }
}
