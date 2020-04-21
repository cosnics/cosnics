<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;

/**
 *
 * @package Chamilo\Libraries\Storage\Query
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
abstract class ConditionPart implements Hashable
{
    use ClassContext;
    use HashableTrait;

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Interfaces\Hashable::getHashParts()
     */
    public function getHashParts()
    {
        return array(static::class_name());
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return static::context();
    }
}

