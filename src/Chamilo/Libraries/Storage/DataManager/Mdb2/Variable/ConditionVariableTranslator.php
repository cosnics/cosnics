<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2\Variable;

use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Mdb2\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ConditionVariableTranslator extends \Chamilo\Libraries\Storage\Query\Variable\ConditionVariableTranslator
{

    public static function render(ConditionVariable $condition_variable)
    {
        return parent :: factory(DataManager :: TYPE_MDB2, $condition_variable)->translate();
    }
}
