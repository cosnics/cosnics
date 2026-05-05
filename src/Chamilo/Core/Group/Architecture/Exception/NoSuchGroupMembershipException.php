<?php
namespace Chamilo\Core\Group\Architecture\Exception;

use Chamilo\Core\Group\Storage\DataClass\GroupMembership;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
use Exception;

/**
 * @package Chamilo\Core\Group\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NoSuchGroupMembershipException extends NoSuchObjectException
{
    public function __construct(
        array $objectIdentifiers, ?string $message = null, int $code = 0, ?Exception $previousException = null
    )
    {
        parent::__construct(GroupMembership::class, $objectIdentifiers, $message, $code, $previousException);
    }
}