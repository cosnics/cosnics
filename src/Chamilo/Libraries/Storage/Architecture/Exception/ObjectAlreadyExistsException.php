<?php
namespace Chamilo\Libraries\Storage\Architecture\Exception;

use Exception;
use Throwable;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ObjectAlreadyExistsException extends Exception
{
    public function __construct(
        public string $dataClassStorageUnitName, public array $record, string $message = '', int $code = 0,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}