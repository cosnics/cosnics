<?php
namespace Chamilo\Core\User\Architecture\Exception;

use Chamilo\Core\User\Storage\DataClass\UserVisit;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
use Exception;

/**
 * @package Chamilo\Core\User\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NoSuchUserVisitException extends NoSuchObjectException
{
    public function __construct(
        array $objectIdentifiers, ?string $message = null, int $code = 0, ?Exception $previousException = null
    )
    {
        parent::__construct(UserVisit::class, $objectIdentifiers, $message, $code, $previousException);
    }
}