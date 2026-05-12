<?php
namespace Chamilo\Libraries\Storage\Architecture\Exception;

use Exception;
use Throwable;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EntityAlreadyExistsException extends Exception
{
    public function __construct(
        public string $entityClassname, public object $entity, string $message = '', int $code = 0,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}