<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;

/**
 * Parent class for all abstract condition variables used to build conditions for the storage layer
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ConditionVariable implements Hashable
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;
    use \Chamilo\Libraries\Architecture\Traits\HashableTrait;

    public function getHashParts()
    {
        return array(static :: class_name());
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return static :: context();
    }
}
