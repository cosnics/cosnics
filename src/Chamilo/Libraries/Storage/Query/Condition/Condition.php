<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;

/**
 * Parent class for all abstract conditions used to retrieve objects from the storage layer
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package common.libraries
 */
abstract class Condition implements Hashable
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;
    use \Chamilo\Libraries\Architecture\Traits\HashableTrait;

    /**
     *
     * @return string[]
     */
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
