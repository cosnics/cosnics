<?php
namespace Chamilo\Core\Group\Architecture\Exception;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
use Exception;

/**
 * @package Chamilo\Core\Group\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NoSuchGroupException extends NoSuchObjectException
{
    public function __construct(
        ?array $criteria = null, ?string $query = null, ?string $message = null, int $code = 0,
        ?Exception $previousException = null
    )
    {
        parent::__construct(Group::class, $criteria, $query, $message, $code, $previousException);
    }
}