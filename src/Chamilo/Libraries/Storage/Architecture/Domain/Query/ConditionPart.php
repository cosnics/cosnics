<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query;

use Chamilo\Libraries\Protocol\Security\Architecture\Interface\HashableInterface;
use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;

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

