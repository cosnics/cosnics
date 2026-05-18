<?php
namespace Chamilo\Libraries\Storage\Architecture\Exception;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Storage\Service\NoSuchObjectExceptionRenderer;
use Exception;

/**
 * This class represents an object not exists exception.
 * Throw this if you retrieved an object from the request
 * parameter that is not valid
 *
 * @package Chamilo\Libraries\Storage\Architecture\Exception
 */
class NoSuchObjectException extends Exception implements UserExceptionInterface
{
    public function __construct(
        public string $objectType, public ?array $criteria = null, public ?string $query = null, ?string $message = null, int $code = 0,
        ?Exception $previousException = null
    )
    {
        parent::__construct($message, $code, $previousException);
    }

    public function getUserExceptionRendererClassName(): string
    {
        return NoSuchObjectExceptionRenderer::class;
    }
}
