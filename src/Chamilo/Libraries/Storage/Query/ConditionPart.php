<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\HashableInterface;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;

/**
 * @package Chamilo\Libraries\Storage\Query
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
abstract class ConditionPart implements HashableInterface
{
    use HashableTrait;

    /**
     * @return string[]
     */
    public function getHashParts(): array
    {
        return [static::class];
    }
}

