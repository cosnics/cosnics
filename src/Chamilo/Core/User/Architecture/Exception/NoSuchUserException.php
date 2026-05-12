<?php
namespace Chamilo\Core\User\Architecture\Exception;

use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
use Exception;

/**
 * @package Chamilo\Core\User\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NoSuchUserException extends NoSuchObjectException
{
    public function __construct(
        array $objectIdentifiers, ?string $message = null, int $code = 0, ?Exception $previousException = null
    )
    {
        parent::__construct(User::class, $objectIdentifiers, $message, $code, $previousException);
    }
}